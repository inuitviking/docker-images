<?php
// Be sure we can have all the necessary libraries required.
require_once 'vendor/autoload.php';
// Require
require_once 'classes/Database.php';
require_once 'classes/CRUD.php';
/*
 * Servers:
 * - Skúlaheim
 *   - 192.168.80.2
 *   - 192.168.80.12
 *   - 192.168.80.17
 * - Hotspot
 *   - 192.168.95.115
 * - Heima
 *   - 192.168.1.222
 * - Skúli
 *   - 10.135.16.54
 */
$dbserver	= '192.168.80.12';
$dbuser		= 'Ahmoo';
$dbpass		= '?&1Q%R>y[lHp,W6KABZy?%l)v#_^';
$db		= 'infoscreen';

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