<?php

echo "Sleep before doing anything. We want the other services to be ready.\n";
for ($i = 1; $i <= 5; $i++) {
	echo $i."\n";
	sleep(1);
}
echo "Ready!\n";


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
		->setTlsCertificateAuthorityFile("certs/ca-root-cert.crt");							// Root certificate for the client and server certificate;					// Set client certificate key

	$mqtt->connect($connectionSettings, true);															// Connect to the MQTT broker with the above connection settings and with a clean session.

	$mqtt->subscribe('hospital/#', function ($topic, $message) use ($bpmCrud) {								// Recursively subscribe to hospital/

		$topic = explode('/', $topic);																		// Explode the topic
		$bpm = $bpmCrud->Read(['*'], "WHERE bed = '" . $topic[2] . "'", 1);								// Check if the value exists in the db

		if ($topic[1] == 'bpm') {
			if ($bpm == "" || !$bpm) {																						// If it does, update the entry; if not create the entry
				$bpmCrud->Create(['bed' => $topic[2],'bpm' => $message]) . "\n";
			} else {
				$bpmCrud->Update(['bpm' => $message], "WHERE bed = '".$topic[2]."'") . "\n";
			}
		} else if ($topic[1] == 'call') {
			if ($bpm == "" || !$bpm) {																						// If it does, update the entry; if not create the entry
				$bpmCrud->Create(['bed' => $topic[2],'call' => $message]) . "\n";
			} else {
				$bpmCrud->Update(['call' => $message], "WHERE bed = '".$topic[2]."'") . "\n";
			}
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
