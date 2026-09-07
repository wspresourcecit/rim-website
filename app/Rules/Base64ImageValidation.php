<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Base64ImageValidation implements ValidationRule
{
    protected $maxSize; // Maximum allowed file size in kilobytes

    /**
     * Allowed raster image types. SVG is intentionally excluded — it can carry
     * script and is an XSS vector when served from the app origin.
     */
    protected const ALLOWED_TYPES = ['jpeg', 'jpg', 'png', 'gif', 'webp'];

    /**
     * Create a new rule instance.
     *
     * @param int $maxSize Maximum size in kilobytes (default: 10 MB)
     */
    public function __construct($maxSize = 10240)
    {
        $this->maxSize = $maxSize;
    }

    /**
     * Run the validation rule.
     *
     * @param string $attribute
     * @param mixed $value
     * @param \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value) || $value === '') {
            $fail('The :attribute must be a valid base64-encoded image.');
            return;
        }

        // Must be a data URI declaring one of the allowed image types.
        $pattern = '/^data:image\/(' . implode('|', self::ALLOWED_TYPES) . ');base64,/i';
        if (!preg_match($pattern, $value)) {
            $fail('The :attribute must be a valid base64-encoded image (JPEG, PNG, GIF, or WEBP).');
            return;
        }

        // Strict base64 decode — rejects malformed / smuggled payloads.
        $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $value), true);

        if ($imageData === false) {
            $fail('The :attribute is not valid base64 data.');
            return;
        }

        if (strlen($imageData) > $this->maxSize * 1024) {
            $fail('The :attribute must not exceed ' . $this->maxSize . ' KB.');
            return;
        }

        // The bytes must actually decode as a real raster image, not just
        // claim an image MIME type in the data-URI header.
        $info = @getimagesizefromstring($imageData);
        if ($info === false) {
            $fail('The :attribute is not a readable image file.');
            return;
        }

        $detected = str_replace('image/', '', (string) ($info['mime'] ?? ''));
        if (!in_array(strtolower($detected), self::ALLOWED_TYPES, true)) {
            $fail('The :attribute has an unsupported image type.');
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute must be a valid base64-encoded image (JPEG, PNG, GIF, or WEBP) with a maximum size of ' . $this->maxSize . ' KB.';
    }
}
