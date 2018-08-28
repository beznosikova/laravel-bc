<?php
namespace App\Services\Helpers;

use Storage;
use App\Services\Helpers\LoadedData;

class LoadedRate extends LoadedData
{
    protected $columnMatching = 
        [
            0 => 'bc_id_from', 
            1 => 'bc_id_to', 
            2 => 'bc_id_exchange', 
            3 => 'rate_from', 
            4 => 'rate_to'
        ];

	public function getCollectionFromData():array
	{
        set_time_limit(0);

        $page = 1;

        while (count($csvRows = $this->getCsvRows(Storage::path($this->pathToFile), $page)) > 0) {
            foreach ($csvRows as $row) {
                $this->selectBestRate($row);
            }
            $page++;        
        };

        return $this->getClearnData()->toArray();
	}

    private function selectBestRate($row)
    {
        $key = "{$row[0]}-{$row[1]}";

        $existedRow = $this->dataSet->only($key);

        if ($existedRow->isEmpty()){

            $this->dataSet = $this->dataSet->merge(["{$row[0]}-{$row[1]}" => $row]);

        } elseif ($this->compareRates($existedRow->get($key), $row)) {

            $this->dataSet->forget($key);
            $this->dataSet = $this->dataSet->merge(["{$row[0]}-{$row[1]}" => $row]);

        }        
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