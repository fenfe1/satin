<?php

use Satin\BASPeopleAPI\v1\PeopleAPI;

require '../vendor/autoload.php';  // Load Composer

// API credentials
// TODO: Read from dot file for safety
$apiUsername = 'basweb';
$apiPassword = 'password';

// Create instance of the API
$peopleAPI = new PeopleAPI($apiUsername, $apiPassword);

echo "\n";
echo "Welcome" . " \n";

echo "\n";
echo "Fetching token ..." . " \n";
echo $peopleAPI->showToken() . " \n";

echo "\n";
