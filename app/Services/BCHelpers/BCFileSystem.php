<?php
namespace App\Services\Helpers;

use Storage;
use App\Services\BCInterfaces\BCFileSystemInterface;
use GuzzleHttp\Client;

class BCFileSystem implements BCFileSystemInterface
{
	public function load(string $url, string $path):string
	{
        $client = new Client(['http_errors' => false]);
        $response = $client->get($url);        
        
        if ($response->getStatusCode() !== 200)
            return "";

        return Storage::put($path, (string)$response->getBody());
	}

	public function extract(string $url, string $path)
	{
        $url = Storage::path($url);
        return Storage::extractTo($url, $path);
	}
}