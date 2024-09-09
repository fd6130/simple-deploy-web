<?php

namespace App\Utilities\Upload;

use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Exceptions\BadRequestException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile as File;
use Illuminate\Contracts\Filesystem\Filesystem;

class FileStorage
{
    public static function storage(): Filesystem
    {
        return Storage::disk('s3');
    }

    /**
     * Upload file to file storage.
     *
     * @param File $file
     * @return array
     */
    public static function upload(File $file, string $folder = 'upload'): array
    {
        $hashName = $file->hashName();
        $response = self::storage()->putFileAs(Carbon::now()->format('Y/m/d'), $file, $hashName);

        if (!$response)
        {
            throw new BadRequestException('Fail to upload file. Please try again later.');
        }

        return [
            'original_name' => $file->getClientOriginalName(),
            'file_name' => $hashName,
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'path' => $response,
            'access_link' => self::storage()->url($response),
        ];
    }

    public static function shortenFilename(string $filename, int $length = 50): string
    {
        $totalLength = mb_strlen($filename);
        $extension = Str::substr($filename, strrpos($filename, '.'), $totalLength);
        $filename = Str::substr($filename, 0, strrpos($filename, '.'));

        if ($totalLength > $length)
        {
            $filename = Str::substr($filename, 0, $length);
        }

        return $filename . $extension;
    }

    public static function remove(string $path): bool
    {
        if (empty($path) === true) return false;

        return self::storage()->delete($path);
    }

    public static function download(string $path)
    {
        return self::storage()->download($path);
    }
}
