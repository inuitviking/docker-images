<?php
// Be sure we can have all the necessary libraries required.
require_once 'vendor/autoload.php';

$path			= ltrim($_SERVER['REQUEST_URI'],'/');
$urlParams		= ltrim(substr($path, strpos($path, '?')), '?');
$urlParams		= explode('&', $urlParams);
if (str_contains($path, "?")) {
	$path = substr($path, 0, strpos($path, "?"));
}
$elements	= preg_split('/ [\/|?&] /', $path);

// Define the app
$app = new Comet\Comet([
	'host' => '0.0.0.0',
	'port' => 8080,
]);

// Setup routing for /bpm
$app->get($path,
	function ($request, $response) {
		$file = 'mqtt.csv';
		$f = fopen($file, 'r');
		$data = null;
		while (($row = fgetcsv($f)) !== false) {
			$data[] = $row;
		}
		fclose($f);
		return $response
			->with($data);
	});

// Start the app
$app->run();


function GetData (string $path) {
	$data = null;
	
	return $data;
}