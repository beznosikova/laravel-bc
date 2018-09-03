<?php

namespace App\Services\BCInterfaces;

interface BCFileSystemInterface
{
	public function load(string $url, string $path):string;
	public function extract(string $url, string $path);
}