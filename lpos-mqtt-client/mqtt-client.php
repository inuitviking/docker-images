<?php

echo "Sleep before doing anything. We want the other services to be ready.\n";
for ($i = 0; $i < 10; $i++) {
	echo $i."\n";
	sleep(1);
}

require_once 'vendor/autoload.php';
require_once 'classes/Database.php';
require_once 'classes/CRUD.php';

// Objects
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
// Exceptions
use PhpMqtt\Client\Exceptions\ConfigurationInvalidException;
use PhpMqtt\Client\Exceptions\ConnectingToBrokerFailedException;
use PhpMqtt\Client\Exceptions\DataTransferException;
use PhpMqtt\Client\Exceptions\InvalidMessageException;
use PhpMqtt\Client\Exceptions\MqttClientException;
use PhpMqtt\Client\Exceptions\ProtocolViolationException;
use PhpMqtt\Client\Exceptions\RepositoryException;

// *************
// * VARIABLES *
// *************
/*
 * Servers:
 * - 192.168.80.2
 * - 192.168.95.115
 * - 192.168.80.17
 * - 192.168.1.222
 * - 10.135.16.54
 */
$server		= '10.135.16.54';
$port		= 8883;
$clientId	= 'infoscreen';
$clientPass	= '5k1nnyL4773';

$dbserver	= $server;
$dbuser		= 'Ahmoo';
$dbpass		= '?&1Q%R>y[lHp,W6KABZy?%l)v#_^';
$db		= 'infoscreen';

$database = new Database($db, $dbuser, $dbpass, $dbserver);
$bpmCrud = new Crud($database, 'bpm');

// ***********
// * PROGRAM *
// ***********
try {
	$mqtt = new MqttClient($server, $port, $clientId);																	// Create MqttClient object

	$connectionSettings = (new ConnectionSettings)																		// Create a ConnectionSettings object
		->setUsername($clientId)																						// Set username
		->setPassword($clientPass)																						// Set password
		->setUseTls(true)																						// Use TLS
		->setTlsSelfSignedAllowed(true)																// Allow self-signed certificates
		->setTlsCertificateAuthorityFile("certs/ca-root-cert.crt");				// Root certificate for the client and server certificate;					// Set client certificate key

	$mqtt->connect($connectionSettings, true);															// Connect to the MQTT broker with the above connection settings and with a clean session.
	$mqtt->subscribe('hospital/#', function ($topic, $message) use ($bpmCrud) {												// Recursively subscribe to hospital/
		file_put_contents('mqtt.csv', "$topic,$message", LOCK_EX);
		$topic = explode('/', $topic);
		echo "Topic: ". $topic[3] . "\n";
		$bpms = $bpmCrud->Read(['*'], "WHERE bed = '" . $topic[3] . "'", 1);

		foreach ($bpms as $bpm) {
			print_r($bpm);
		}

	}, 0);																								// Set the QoS to 0

	$mqtt->loop(true);																						// Continuously listen for messages
	$mqtt->disconnect();																								// Properly disconnect from the broker if three CTRL+C are detected

} catch (ConfigurationInvalidException |																				// Catch any exception that may occur in any of the functions used above.
		 ConnectingToBrokerFailedException |
		 DataTransferException |
		 RepositoryException |
		 InvalidMessageException |
		 ProtocolViolationException |
		 MqttClientException $e) {
	echo $e;																											// Echo the error
	exit(0);																											// Exit the program with exit code 0
}


function insertBPM (string $topic, string $message): void {
	$topic = explode('/', $topic);
	$topic = array_filter($topic);

	print_r($topic);

	$select = sprintf("SELECT bed FROM bpm WHERE bed = '%s'", $topic[3]);
	$insert = sprintf("INSERT INTO bpm (bed, bpm) VALUES ('%s', '%s')", $topic[3], $message);
	$update = sprintf("UPDATE bpm SET bpm='%s' WHERE bed = '%s'", $message, $topic[3]);

	echo $select."\n";
	echo $insert."\n";
	echo $update."\n";

	$result = $db->query($select);

	print_r(mysqli_num_rows($result));

	if (mysqli_num_rows($result) >= 1) {
		echo $db->query($insert);
		echo mysqli_query($db, $insert);
	}else {
		echo $db->query($update);
		echo mysqli_query($db, $update);
	}
}