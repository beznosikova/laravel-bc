<?php
namespace App\Services;

use DB;
// use ZipArchive;
use Storage;

use App\{Exchange, Currency, Rate};
use App\Services\Helpers\{
    LoadedCurrency, 
    LoadedExchange, 
    LoadedRate, 
    LoadedData};
use App\Services\BCInterfaces\BCFileSystemInterface;

use Illuminate\Support\Facades\Config;

class BestChange
{
    const BC_URL = 'http://www.bestchange.ru/bm/info.zip';

    const TMP_PATH = "tmp". DIRECTORY_SEPARATOR;
    const TMP_FILE = self::TMP_PATH . "tmp_file.zip";
    const TMP_UNZIPPED = self::TMP_PATH . "unzipped";
    
    const TMP_FILE_CURRENCY = self::TMP_UNZIPPED . DIRECTORY_SEPARATOR . "bm_cy.dat";
    const TMP_FILE_EXCHANGE = self::TMP_UNZIPPED . DIRECTORY_SEPARATOR . "bm_exch.dat";
    const TMP_FILE_RATES = self::TMP_UNZIPPED . DIRECTORY_SEPARATOR . "bm_rates.dat";

    protected $fileClient;

    public function __construct(BCFileSystemInterface $fileClient)
    {
        $this->fileClient = $fileClient;
    }

    public function run()
    {
        if ($this->loadFiles()){
            // $this->loadDataDB((new LoadedCurrency(self::TMP_FILE_CURRENCY)), 'currencies');
            // $this->loadDataDB((new LoadedExchange(self::TMP_FILE_EXCHANGE)), 'exchanges');
            // $this->loadDataDB((new LoadedRate(self::TMP_FILE_RATES)), 'rates', false);
            return true;
        } 
        return false;
    }

    public function loadFiles()
    {
        $loaded = $this->fileClient->load(
                    env('BC_URL', self::BC_URL), 
                    self::TMP_FILE
                );

        if (!$loaded){
            return $loaded;
        } else {
            return $this->fileClient->extract(
                    self::TMP_FILE, 
                    self::TMP_UNZIPPED
                );
        }
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