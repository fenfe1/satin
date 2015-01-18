<?php

namespace Satin;

use Antarctica\BASAPI\v1\PeopleAPI;
use Exception;

class BASPeopleAPI {

    private $username;

    private $password;

    private $secretsFileName = '.secret_credentials.json';

    private $peopleAPIClient;

    function __construct()
    {
        // Load API Credentials
        $this->loadAPICredentials();

        // Create API client
        $this->peopleAPIClient = new PeopleAPI($this->username, $this->password);
    }

    private function loadSecretsFile()
    {
        $scriptDirectory = preg_replace('~(\w)$~' , '$1' . DIRECTORY_SEPARATOR , realpath(getcwd()));
        $scriptParentDirectory = preg_replace( '~[/\\\\][^/\\\\]*[/\\\\]$~' , DIRECTORY_SEPARATOR , $scriptDirectory);

        $fileName = $this->secretsFileName;
        $filePath = $scriptParentDirectory . DIRECTORY_SEPARATOR . $fileName;

        set_error_handler(
            create_function(
                '$severity, $message, $file, $line',
                'throw new ErrorException($message, $severity, $severity, $file, $line);'
            )
        );

        try
        {
            // Read secrets file
            $fileContents = file_get_contents($filePath);
            $fileContents = json_decode($fileContents, $associativeArray = true);

            // Set secrets
            $this->username = $fileContents['username'];
            $this->password = $fileContents['password'];
        }
        catch (Exception $e)
        {
            echo "... Unable to open secrets file - does it exist?" . "\n";
        }

        restore_error_handler();
    }

    private function loadAPICredentials()
    {
        echo "\n";
        echo "Setting API credentials from [" . $this->secretsFileName . "] file ..." . " \n";

        $this->loadSecretsFile();

        if (isset($this->username))
        {
            echo "... API username set [" . $this->username . "]" . " \n";
        }
        if (isset($this->password))
        {
            echo "... API password set [*********]" . " \n";
        }
    }

    public function listEveryone()
    {
        echo "\n";
        echo "Listing everyone in the database ..." . " \n";

        $everyone = $this->peopleAPIClient->getEveryone();
        foreach ($everyone as $person)
        {
            echo "... [" . $person['id'] ."] " . $this->getDisplayName($person) . " \n";
        }
    }

    public function showPerson($reference)
    {
        echo "\n";
        echo "Showing details for '" . $reference . "' ..." . " \n";

        $person = $this->peopleAPIClient->getPerson('eakf');

        echo "... ID        : " . $person['id'] . " \n";
        echo "... Name      : " . $this->getDisplayName($person) . " \n";
        echo "... Job Title : " . $person['job_title'] . " \n";
    }

    public function getDisplayName($person)
    {
        $name = [
            'title' => $person['name_title'],
            'first' => $person['name_first'],
            'middle' => $person['name_middle'],
            'last' => $person['name_last']
        ];

        if (isset($name['middle']) === false)
        {
            unset($name['middle']);
        }

        if (isset($person['pref_name_title']))
        {
            $name['title'] = $person['pref_name_title'];
        }
        if (isset($person['pref_name_first']))
        {
            $name['first'] = $person['pref_name_first'];
        }
        if (isset($person['pref_name_last']))
        {
            $name['last'] = $person['pref_name_last'];
        }

        $name['title'] = $name['title'] . '.';

        return implode(' ', $name);
    }
}
