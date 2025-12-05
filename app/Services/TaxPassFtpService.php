<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class TaxPassFtpService
{
    /**
     * Upload a file to the TaxPass FTP server.
     *
     * @param  string  $content  The file content
     * @param  string  $fileName  The target filename
     * @return bool True on success
     */
    public function upload(string $content, string $fileName): bool
    {
        return Storage::disk('taxpass_ftp')->put($fileName, $content);
    }
}
