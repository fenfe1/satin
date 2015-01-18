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
echo "Listing everyone in the database ..." . " \n";
$everyone = $peopleAPI->getEveryone();
foreach ($everyone as $person)
{
    echo "... [" . $person['id'] ."] " . $person['name_title'] . '. ' . $person['name_first'] . ' ' . $person['name_middle'] . ' ' . $person['name_last'] . " \n";
}

echo "\n";
echo "Showing details for 'eakf' ..." . " \n";
$eakf = $peopleAPI->getPerson('eakf');

var_dump([
    'ID' => $eakf['id'],
    'Name' => $eakf['name_title'] . '. ' . $eakf['name_first'] . ' ' . $eakf['name_middle'] . ' ' . $eakf['name_last'],
    'Job Title' => $eakf['job_title']
]);
