<?php
// Be sure we can have all the necessary libraries required.
require_once 'vendor/autoload.php';
// Require custom classes
require_once 'classes/Database.php';
require_once 'classes/CRUD.php';
// *************
// * VARIABLES *
// *************
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
$db			= 'infoscreen';

$database = new Database($db, $dbuser, $dbpass, $dbserver);
$bpmCrud = new Crud($database, 'bpm');

// ***********
// * PROGRAM *
// ***********
// App settings
$app = new Comet\Comet([
	'host' => '0.0.0.0',
	'port' => 8080,
]);

// What to do on /bpm
$app->get('/bpm',
	function ($request, $response) use ($bpmCrud) {
		$data = $bpmCrud->Read(['*']);
		return $response
			->with($data);
	});

// Start the service
$app->run();
