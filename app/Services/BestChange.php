<?php
namespace App\Services;

use Storage;
use ZipArchive;
use Illuminate\Support\Facades\Config;

class BestChange
{
    const BC_URL = 'http://www.bestchange.ru/bm/info.zip';
    const TMP_PATH = "tmp". DIRECTORY_SEPARATOR;
    const TMP_FILE = self::TMP_PATH . "tmp_file.zip";
    const TMP_UNZIPPED = self::TMP_PATH . "unzipped";

    public function __construct()
    {
        dump('constructor');
    }

    public function loadFiles()
    {
        $filePath = self::TMP_FILE;
        Storage::put($filePath, file_get_contents(self::BC_URL));

        $url = Storage::path($filePath);
        return Storage::extractTo(self::TMP_UNZIPPED, $url);
    }

}