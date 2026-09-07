<?php

use App\Exceptions\ApiException;
use App\Models\Branch;
use Illuminate\Support\Facades\Cache;

if (!function_exists('resolveBranchId')) {
    /**
     * Enforce the branch business rule shared by every Home-page CMS section
     * (Banner, Statistic, DiplomaCta, ...): a branch context is valid only
     * when is_home = true AND is_active = true. branch_id = null represents
     * the Home context.
     *
     * @param mixed $branchId
     * @param string $context used in the error message, e.g. "banner", "diploma CTA"
     */
    function resolveBranchId($branchId, string $context = 'resource'): ?int
    {
        if (empty($branchId)) {
            return null;
        }

        $isValidHomeBranch = Branch::homeActive()->whereKey($branchId)->exists();

        if (!$isValidHomeBranch) {
            throw new ApiException("Selected branch is not available for {$context} management.", 404);
        }

        return (int) $branchId;
    }
}

function format_bdt(string $number, int $decimals = 0, string $symbol = '৳'): string
{
    $parts = explode('.', number_format($number, $decimals, '.', ''));

    $integerPart = $parts[0];
    $decimalPart = isset($parts[1]) ? $parts[1] : null;

    // Get the last 3 digits
    $lastThree = substr($integerPart, -3);
    $restUnits = substr($integerPart, 0, -3);

    if ($restUnits != '') {
        $restUnits = preg_replace("/\B(?=(\d{2})+(?!\d))/", ",", $restUnits);
        $formatted = $restUnits . ',' . $lastThree;
    } else {
        $formatted = $lastThree;
    }

    if ($decimals > 0 && $decimalPart) {
        $formatted .= '.' . $decimalPart;
    }

    return $formatted . $symbol;
}

if (!function_exists('convertToBaseUnit')) {
    /**
     * Convert quantity from sub unit to base unit.
     * @param float $unit The quantity in sub unit.
     * @param int $baseUnitId The ID of the base unit.
     * @param int $productUnitId The ID of the product's unit.
     * @param int|null $operator The operator for conversion (1 = multiplication, 2 = division).
     * @param float|null $operatorValue The value used in the conversion operation.
     * @return float The quantity converted to base unit.
     */
    function convertToBaseUnit(?float $unit, int $baseUnitId, int $productUnitId, ?int $operator, ?float $operatorValue): float
    {
        if ($baseUnitId === $productUnitId || is_null($operator) || is_null($operatorValue) || is_null($unit)) {
            return $unit ?? 0;
        }

        switch ($operator) {
            case 1: // multiplication
                return $unit * $operatorValue;
            case 2: // division
                if ($operatorValue != 0) {
                    return $unit / $operatorValue;
                }
                return $unit; // Avoid division by zero
            default:
                return $unit; // Unknown operator, return original quantity
        }
    }
}

if (!function_exists('convertToSubUnit')) {
    /**
     * Convert quantity from base unit to sub unit.
     * @param float $unit The quantity in base unit.
     * @param int $baseUnitId The ID of the base unit.
     * @param int $productUnitId The ID of the product's unit.
     * @param int|null $operator The operator for conversion (1 = multiplication, 2 = division).
     * @param float|null $operatorValue The value used in the conversion operation.
     * @return float The quantity converted to sub unit.
     */
    function convertToSubUnit(?float $unit, int $baseUnitId, int $productUnitId, ?int $operator, ?float $operatorValue): float
    {
        if ($baseUnitId === $productUnitId || is_null($operator) || is_null($operatorValue) || is_null($unit)) {
            return $unit ?? 0;
        }

        switch ($operator) {
            case 1: // multiplication
                if ($operatorValue != 0) {
                    return $unit / $operatorValue;
                }
                return $unit; // Avoid division by zero
            case 2: // division
                return $unit * $operatorValue;
            default:
                return $unit; // Unknown operator, return original quantity
        }
    }
}

if (!function_exists('formatNumber')) {
    /**
     * Format phone number.
     * @param string $number The phone number to format.
     * @return string The formatted phone number.
     */
    function formatNumber(string $number): string
    {
        if ($number) {
            $number = str_replace([' ', '-', '(', ')'], '', $number);
            return preg_replace('/^(?:\+?880|0)?/', '+880', $number);
        }
        return $number;
    }
}

if (!function_exists('formatNumberWithouCode')) {
    /**
     * Format phone number.
     * @param string $number The phone number to format.
     * @return string The formatted phone number.
     */
    function formatNumberWithouCode(string $number): string
    {
        if ($number) {
            $number = str_replace([' ', '-', '(', ')'], '', $number);
            return preg_replace('/^(?:\+?880|0)?/', '0', $number);
        }
        return $number;
    }
}

if (!function_exists('numberToWords')) {
    /**
     * Convert number to words.
     * @param int $num The number to convert.
     * @return string The number converted to words.
     */
    function numberToWords(int $num): string
    {
        $ones = [
            0 => "zero",
            1 => "one",
            2 => "two",
            3 => "three",
            4 => "four",
            5 => "five",
            6 => "six",
            7 => "seven",
            8 => "eight",
            9 => "nine",
            10 => "ten",
            11 => "eleven",
            12 => "twelve",
            13 => "thirteen",
            14 => "fourteen",
            15 => "fifteen",
            16 => "sixteen",
            17 => "seventeen",
            18 => "eighteen",
            19 => "nineteen"
        ];

        $tens = [
            2 => "twenty",
            3 => "thirty",
            4 => "forty",
            5 => "fifty",
            6 => "sixty",
            7 => "seventy",
            8 => "eighty",
            9 => "ninety"
        ];

        $thousands = ["", "thousand", "million", "billion", "trillion"];

        if ($num == 0) {
            return "Zero";
        }

        $words = "";
        $i = 0;

        while ($num > 0) {
            $rem = $num % 1000;
            if ($rem != 0) {
                $str = "";
                if ($rem >= 100) {
                    $str .= $ones[(int)($rem / 100)] . " hundred ";
                    $rem %= 100;
                }
                if ($rem > 0) {
                    if ($rem < 20) {
                        $str .= $ones[$rem] . " ";
                    } else {
                        $str .= $tens[(int)($rem / 10)] . " ";
                        if ($rem % 10 > 0) {
                            $str .= $ones[$rem % 10] . " ";
                        }
                    }
                }
                $words = $str . $thousands[$i] . " " . $words;
            }
            $num = (int)($num / 1000);
            $i++;
        }

        return ucfirst(trim($words));
    }
}


if (!function_exists('isDifferentArray')) {

    function isDifferentArray(array $a, array $b): bool
    {
        // If arrays contain the same values (regardless of order), return false
        if (count($a) === count($b) && !array_diff($a, $b) && !array_diff($b, $a)) {
            return false;
        }

        // Otherwise return true
        return true;
    }
}


if (!function_exists('getGlobalVersion')) {
    function getGlobalVersion(string $module): int
    {
        return Cache::rememberForever("{$module}:version", fn() => 1);
    }
}

if (!function_exists('isRoute')) {
    /**
     * Whether the current request matches any of the given path patterns
     * (wildcards allowed, e.g. "courses", "course/*") or route names.
     */
    function isRoute(string ...$patterns): bool
    {
        return request()->is(...$patterns) || request()->routeIs(...$patterns);
    }
}

if (!function_exists('navActive')) {
    /**
     * Return $active when the current request matches one of the patterns,
     * otherwise $inactive. Handy for header/menu link classes.
     */
    function navActive(array|string $patterns, string $active = 'text-primary-50', string $inactive = 'text-black-50'): string
    {
        return isRoute(...(array) $patterns) ? $active : $inactive;
    }
}
