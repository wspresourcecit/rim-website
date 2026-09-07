<?php

use Intervention\Image\Laravel\Facades\Image;


use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

function isBase64Image($value): bool
{
    return is_string($value) && str_starts_with($value, 'data:image/');
}

/**
 * Normalise a client-supplied extension to a known-safe raster image type.
 * Anything unrecognised falls back to "jpg" so nothing executable/markup
 * is ever written into the public web root.
 */
function safeImageExtension($extension): string
{
    $extension = strtolower(preg_replace('/[^A-Za-z0-9]/', '', (string) $extension));

    return in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true) ? $extension : 'jpg';
}

function getImageManager(): ImageManager
{
    return new ImageManager(
        new Driver()
    );
}

function uploadFile($file, $prefix = '', $dir = 'documents')
{
    // Never write executable / markup extensions into the public web root,
    // regardless of what the client claims. FormRequests still do the
    // primary mime check; this is defence in depth.
    $blocked = [
        'php',
        'php3',
        'php4',
        'php5',
        'php7',
        'php8',
        'phtml',
        'phar',
        'pht',
        'phps',
        'cgi',
        'pl',
        'py',
        'rb',
        'sh',
        'bash',
        'exe',
        'bat',
        'cmd',
        'com',
        'jsp',
        'asp',
        'aspx',
        'htaccess',
        'html',
        'htm',
        'shtml',
        'svg',
        'xml',
    ];

    $extension = strtolower(preg_replace('/[^A-Za-z0-9]/', '', (string) $file->getClientOriginalExtension()));

    if ($extension === '' || in_array($extension, $blocked, true)) {
        abort(422, 'This file type is not allowed.');
    }

    $prefix = preg_replace('/[^A-Za-z0-9_\-]/', '', (string) $prefix);
    $fileName = $prefix . bin2hex(random_bytes(16)) . '.' . $extension;

    $path = public_path($dir . '/');

    if (!file_exists($path)) {
        mkdir($path, 0755, true);
    }
    $file->move($path, $fileName);

    return $dir . '/' . $fileName;
}

function uploadBase64Image(
    $base64,
    $dir = 'images',
    $prefix = '',
    $width = null,
    $height = null,
    $extn = 'webp'
) {
    $uniqueId = uniqid();

    $imageName = $prefix . '_' . $uniqueId . '.' . $extn;

    $path = public_path($dir . '/');

    if (!file_exists($path)) {
        mkdir($path, 0755, true);
    }

    $manager = getImageManager();


    $image = $manager->decode($base64);

    if (is_numeric($width) && is_numeric($height)) {
        $image->resize(
            (int) $width,
            (int) $height
        );
    }

    $image->save($path . $imageName);

    return $dir . '/' . $imageName;
}

function uploadImage($file, $width = null, $height = null, $prefix = '', $dir = 'images')
{
    $uniqueId = uniqid();
    $imageName = $prefix . '_' . $uniqueId . '.' . safeImageExtension($file->getClientOriginalExtension());
    $path = public_path($dir . '/');

    if (!file_exists($path)) {
        mkdir($path, 0755, true);
    }
    $image = Image::read($file);

    if ($width || $height) {
        $image->resize($width, $height);
    }

    $image->save($path . $imageName);
    return $dir . '/' . $imageName;
}

function deleteImage($path): bool
{
    if ($path && file_exists(public_path($path))) {
        unlink(public_path($path));
        return true;
    }
    return false;
}

function deleteFile($filePath): bool
{
    return  deleteImage($filePath);
}

function uploadImages($files, $width, $height, $prefix = '', $dir = 'images')
{
    $uploadedImages = [];
    $path = public_path($dir . '/');

    // Ensure the directory exists
    if (!file_exists($path)) {
        mkdir($path, 0755, true);
    }

    foreach ($files as $file) {
        $imageName = $prefix . time() . '_' . uniqid() . '.' . safeImageExtension($file->getClientOriginalExtension());
        Image::read($file)->resize($width, $height)->save($path . $imageName);
        $uploadedImages[] = $dir . '/' . $imageName;
    }

    return $uploadedImages;
}
