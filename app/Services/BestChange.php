<?php
namespace App\Services;

use DB;
use Storage;
use Excel;
use App\Exchange;
use App\Currency;
use ZipArchive;
use Illuminate\Support\Facades\Config;

use League\Csv\Reader;
use League\Csv\Statement;

class BestChange
{
    const BC_URL = 'http://www.bestchange.ru/bm/info.zip';
    const TMP_PATH = "tmp". DIRECTORY_SEPARATOR;
    const TMP_FILE = self::TMP_PATH . "tmp_file.zip";
    const TMP_UNZIPPED = self::TMP_PATH . "unzipped";
    const TMP_FILE_CURRENCY = self::TMP_UNZIPPED . DIRECTORY_SEPARATOR . "bm_cy.dat";
    const TMP_FILE_EXCHANGE = self::TMP_UNZIPPED . DIRECTORY_SEPARATOR . "bm_exch.dat";
    const TMP_FILE_RATES = self::TMP_UNZIPPED . DIRECTORY_SEPARATOR . "bm_rates.dat";
    const CHUNK_SIZE = 500;

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
        if (!Currency::all()->first()){//check Currency table
            $csvRows = $this->getCsvRows(Storage::path(self::TMP_FILE_CURRENCY));
            foreach ($csvRows as $row) {
                $data = $this->getClearnData($row, [0, 2], ['bc_id', 'title']);
                if ($data){
                    $dataSet[] = [
                            'bc_id'  => $data['bc_id'],
                            'title'  => $data['title'],
                            'slug'  => str_slug($data['title']),
                        ];
                }
            }
            DB::table('currencies')->insert($dataSet);
        }
        return $this;
    }

    public function loadExchanges()
    {
        if (!Exchange::all()->first()){
            $csvRows = $this->getCsvRows(Storage::path(self::TMP_FILE_EXCHANGE));
            foreach ($csvRows as $row) {
                $data = $this->getClearnData($row, [0, 1], ['bc_id', 'title']);
                if ($data){
                    $dataSet[] = [
                            'bc_id'  => $data['bc_id'],
                            'title'  => $data['title'],
                            'slug'  => str_slug($data['title']),
                        ];
                }
            }
            DB::table('exchanges')->insert($dataSet);
        }
        
        return $this;        
    }

    public function loadRates()
    {
        // Rate::truncate();

        set_time_limit(0);

        $dataSet = collect($value = null);
        $page = 1;

        while (count($csvRows = $this->getCsvRows(Storage::path(self::TMP_FILE_RATES), $page)) > 0) {
            foreach ($csvRows as $row) {
                $key = "{$row[0]}-{$row[1]}";
                $isRow = $dataSet->only($key);
                if ($isRow->isEmpty()){
                    $dataSet = $dataSet->merge(["{$row[0]}-{$row[1]}" => $row]);
                } elseif ($this->compareRates($isRow->get($key), $row)) {
                    $dataSet->forget($key);
                    $dataSet = $dataSet->merge(["{$row[0]}-{$row[1]}" => $row]);
                }
            }
            $page++;
        };

        $rezult = $dataSet->map(function ($item, $key) {
            return $this->getClearnData($item, [0, 1, 2, 3, 4], ['bc_id_from', 'bc_id_to', 'bc_id_exchange', 'rate_from', 'rate_to']);
        });     

        // array_values
        // dump($rezult->toArray());

        dd('saving to db');
        return $this;
    }

    private function getCsvRows(string $path, int $page = 1)
    {
        $csvReader = Reader::createFromPath($path, 'r');
        $csvReader->setDelimiter(';');      
        $csvReader->addStreamFilter('convert.iconv.Windows-1251/UTF-8//TRANSLIT');

        $stmt = (new Statement())
            ->offset(self::CHUNK_SIZE * ($page-1))
            ->limit(self::CHUNK_SIZE);
        return $stmt->process($csvReader);        
    }

    private function getClearnData(array $data, array $keys, array $keyNames)
    {
        if (count($keys) !== count($keyNames)){
            return false;
        }

        $collection = collect($data)->only($keys);
        return collect($keyNames)->combine($collection);
    }

    private function compareRates(array $first, array $second)
    {
        if ($first[3] == 1) {
            return ($first[4] > $second[4]) ? false : true;
        } elseif ($first[4] == 1) {
            return ($first[3] < $second[3]) ? false : true;
        }
    }

}