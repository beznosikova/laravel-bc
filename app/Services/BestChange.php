<?php
namespace App\Services;

use DB;
use Storage;
use App\Currency;
use ZipArchive;
use Illuminate\Support\Facades\Config;

class BestChange
{
    const BC_URL = 'http://www.bestchange.ru/bm/info.zip';
    const TMP_PATH = "tmp". DIRECTORY_SEPARATOR;
    const TMP_FILE = self::TMP_PATH . "tmp_file.zip";
    const TMP_UNZIPPED = self::TMP_PATH . "unzipped";
    const TMP_FILE_CURRENCY = self::TMP_UNZIPPED . DIRECTORY_SEPARATOR . "bm_cy.dat";

    public function __construct()
    {
        dump('constructor');
    }

    public function loadFiles()
    {
        Storage::put(self::TMP_FILE, file_get_contents(self::BC_URL));
        $url = Storage::path(self::TMP_FILE);
        return Storage::extractTo(self::TMP_UNZIPPED, $url);
    }

    public function loadCurrencies()
    {
        //check Currency table
        if (!Currency::all()->first()){
            $csvRows = $this->getCsvRows(Storage::path(self::TMP_FILE_CURRENCY));
            foreach ($csvRows as $row) {
                $data = explode(";", $row[0]);
                $title = iconv("Windows-1251", "UTF-8", $data[2]);
                $dataSet[] = [
                        'bc_id'  => $data[0],
                        'title'  => $title,
                        'slug'  => str_slug($title),
                    ];
            }
            DB::table('currencies')->insert($dataSet);
        }
        return $this;
    }

    private function getCsvRows(string $path)
    {
        return array_map('str_getcsv', file($path));
    }

}