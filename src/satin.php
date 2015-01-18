<?php

use Satin\BASPeopleAPI;

require '../vendor/autoload.php';  // Load Composer

$peopleAPI = new BASPeopleAPI();

// List everyone
$peopleAPI->listEveryone();

// Show someone
$peopleAPI->showPerson('eakf');
