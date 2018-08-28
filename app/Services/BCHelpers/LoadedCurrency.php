<?php
namespace App\Services\Helpers;

use Storage;
use App\Services\Helpers\LoadedData;

class LoadedCurrency extends LoadedData
{
    protected $columnMatching = 
        [
            0 => 'bc_id', 
            2 => 'title', 
        ];

	public function getCollectionFromData():array
	{
        $csvRows = $this->getCsvRows(Storage::path($this->pathToFile));
        foreach ($csvRows as $row) {
            $this->dataSet->push($row);
        }       
        return $this->getClearnData()->toArray();
	}
}