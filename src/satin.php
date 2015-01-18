<?php

use Satin\BASPeopleAPI\v1\PeopleAPI;

require '../vendor/autoload.php';  // Load Composer

// API credentials
$apiUsername = null;
$apiPassword = null;

echo "\n";
echo "Setting API credentials from [.secret_credentials.json] file ..." . " \n";
$secrets = loadFromSecretFile();
$apiUsername = $secrets['username'];
$apiPassword = $secrets['password'];

if ($apiUsername !== null)
{
    echo "... API username set [" . $apiUsername . "]" . " \n";
}
if ($apiPassword !== null)
{
    echo "... API password set [*********]" . " \n";
}

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
// Misc functions
function loadFromSecretFile()
{
    // This is *huge* hack (though it doesn't help that file_get_contents() doesn't through exceptions)

    $secrets = [
        'username' => null,
        'password' => null
    ];

    $scriptDirectory = preg_replace('~(\w)$~' , '$1' . DIRECTORY_SEPARATOR , realpath(getcwd()));
    $scriptParentDirectory = preg_replace( '~[/\\\\][^/\\\\]*[/\\\\]$~' , DIRECTORY_SEPARATOR , $scriptDirectory);

    $fileName = '.secret_credentials.json';
    $filePath = $scriptParentDirectory . DIRECTORY_SEPARATOR . $fileName;

    set_error_handler(
        create_function(
            '$severity, $message, $file, $line',
            'throw new ErrorException($message, $severity, $severity, $file, $line);'
        )
    );

    try
    {
        $fileContents = file_get_contents($filePath);

        $fileContents = json_decode($fileContents, $associativeArray = true);

        // Return credentials
        $secrets['username'] = $fileContents['username'];
        $secrets['password'] = $fileContents['password'];
    }
    catch (Exception $e)
    {
        echo "... Unable to open secrets file - does it exist?" . "\n";

        $secrets['username'] = null;
        $secrets['password'] = null;
    }

    restore_error_handler();

    return $secrets;
}
