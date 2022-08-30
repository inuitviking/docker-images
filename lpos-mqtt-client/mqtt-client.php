<?php

echo "Sleep before doing anything. We want the other services to be ready.\n";
sleep(10);

require_once 'vendor/autoload.php';

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

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$mysqli = new mysqli($server ,$dbuser, $dbpass, $db)
	or die('Error connecting to MySQL server.');

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
	$mqtt->subscribe('hospital/#', function ($topic, $message) use ($db) {												// Recursively subscribe to hospital/
		file_put_contents('mqtt.csv', "$topic,$message", LOCK_EX);
		//		echo "\{$topic:$message}";

		$topic = explode('/', $topic);

		print_r($topic);

		$select = "SELECT bed FROM bpm WHERE bed = ${topic[3]}";
		$insert = "INSERT INTO bpm (bed, bpm) VALUES (${topic[3]}, $message)";
		$update = "UPDATE bpm SET bpm=$message WHERE bed = ${topic[3]}";
		$result = $db->query($select);

		echo $result;

		if ($result) {
			echo $db->query($insert);
		}else {
			echo $db->query($update);
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


//function insertBPM ($db, string $topic, string $payload): void {
//	$topic = explode('/', $topic);
//	$select = "SELECT bed FROM bpm WHERE bed = ${topic[2]}";
//	$insert = "INSERT INTO bpm (bed, bpm) VALUES (${topic[2]}, $payload)";
//	$update = "UPDATE bpm SET bpm=$payload WHERE bed = ${topic[2]}";
//	$result = $db->query($select);
//
//	if ($result) {
//		$db->query($insert);
//	}else {
//		$db->query($update);
//	}
//}