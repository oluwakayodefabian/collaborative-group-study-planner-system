<?php


/**
 * Format a filesize in bytes to a human-readable string.
 *
 * @param int $bytes The number of bytes to format.
 *
 * @return string The formatted filesize.
 */
function formatFileSize($bytes)
{
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' B';
    }
}

/**
 * Removes any characters after a "." in a filename.
 *
 * @param string $filename The filename to modify.
 *
 * @return string The modified filename.
 */
function remove_after_dot($filename)
{
    return explode('.', $filename)[0];
}

/**
 * Fetch the VAPID credentials from the environment variables.
 *
 * @return array The VAPID credentials in the format required by the webpush library.
 */
function fetch_vapid_credentials(): array
{
    return [
        "VAPID" => [
            'subject' => env('VAPID_SUBJECT'),
            'publicKey' => env('VAPID_PUBLIC_KEY'),
            'privateKey' => env('VAPID_PRIVATE_KEY')
        ]
    ];
}
