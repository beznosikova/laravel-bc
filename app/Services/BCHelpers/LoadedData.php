<?php
namespace App\Services\Helpers;

use League\Csv\{Reader, Statement};

abstract class LoadedData
{
	public function getCollectionFromData():array
	{
        $csvRows = $this->getCsvRows(Storage::path($this->pathToFile));
        foreach ($csvRows as $row) {
            $this->dataSet->push($row);
        }		
        return $this->getClearnData()->toArray();
	}
	
    const CHUNK_SIZE = 500;

	protected $dataSet;
	protected $pathToFile;

	public function __construct(string $pathToFile)
	{
		$this->dataSet = collect($value = null);
		$this->pathToFile = $pathToFile;
	}

    protected function getCsvRows(string $path, int $page = 1)
    {
        $csvReader = Reader::createFromPath($path, 'r');
        $csvReader->setDelimiter(';');      
        $csvReader->addStreamFilter('convert.iconv.Windows-1251/UTF-8//TRANSLIT');

        $stmt = (new Statement())
            ->offset(self::CHUNK_SIZE * ($page-1))
            ->limit(self::CHUNK_SIZE);
        return $stmt->process($csvReader);        
    }

    protected function getClearnData()
    {
    	$keys = array_keys($this->columnMatching);
    	$keyNames = array_values($this->columnMatching);

        return $this->dataSet->map(function ($item, $key) use ($keys, $keyNames) {
	        $collection = collect($item)->only($keys);
	        return collect($keyNames)->combine($collection);        	
        });  


    }	
}