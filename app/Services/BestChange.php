<?php
namespace App\Services;

use DB;
use Storage;
use ZipArchive;

use App\{Exchange, Currency, Rate};
use App\Services\Helpers\{LoadedCurrency, LoadedExchange, LoadedRate, LoadedData};

use Illuminate\Support\Facades\Config;
use GuzzleHttp\Client;

class BestChange
{
    const BC_URL = 'http://www.bestchange.ru/bm/info.zip';

    const TMP_PATH = "tmp". DIRECTORY_SEPARATOR;
    const TMP_FILE = self::TMP_PATH . "tmp_file.zip";
    const TMP_UNZIPPED = self::TMP_PATH . "unzipped";
    
    const TMP_FILE_CURRENCY = self::TMP_UNZIPPED . DIRECTORY_SEPARATOR . "bm_cy.dat";
    const TMP_FILE_EXCHANGE = self::TMP_UNZIPPED . DIRECTORY_SEPARATOR . "bm_exch.dat";
    const TMP_FILE_RATES = self::TMP_UNZIPPED . DIRECTORY_SEPARATOR . "bm_rates.dat";

    public function run()
    {
        if ($this->loadFiles()){
            $this->loadDataDB((new LoadedCurrency(self::TMP_FILE_CURRENCY)), 'currencies');
            $this->loadDataDB((new LoadedExchange(self::TMP_FILE_EXCHANGE)), 'exchanges');
            $this->loadDataDB((new LoadedRate(self::TMP_FILE_RATES)), 'rates', false);
            return true;
        } 
        return false;
    }

    public function loadFiles()
    {
        $client = new Client(['http_errors' => false]);
        $response = $client->get(env('BC_URL', self::BC_URL));        
        
        if ($response->getStatusCode() !== 200)
            return;

        Storage::put(self::TMP_FILE, (string)$response->getBody());
        $url = Storage::path(self::TMP_FILE);
        return Storage::extractTo(self::TMP_UNZIPPED, $url);
    }

    public function loadDataDB(
        LoadedData $dataFromFile, 
        string $tableName, 
        bool $checkEmptyTable = true
    ) {
        $haveToWriteData = ($checkEmptyTable) ? !DB::table($tableName)->get()->first() : true;
        if (!$haveToWriteData)
            return;

        $dataCollection = $dataFromFile->getCollectionFromData();

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table($tableName)->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        DB::table($tableName)->insert($dataCollection);
    }

}