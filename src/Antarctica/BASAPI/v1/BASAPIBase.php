<?php

namespace Antarctica\BASAPI\v1;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class BASAPIBase {

    private $scheme = 'http';

    private $endpoint = 'api.bas.ac.uk';

    protected $api;

    private $version = 1;

    private $username;

    private $password;

    protected $client;

    protected $token;

    private $tokenExpiry;

    private $storageDirectory;

    function __construct($username, $password, $storageDirectory = null)
    {
        // Set credentials
        $this->username = $username;
        $this->password = $password;

        // Set storage path
        $this->storageDirectory = $storageDirectory;
        $this->setStoragePath();

        // Create API client
        $this->client = new Client([
            'base_url' => [
                '{scheme}://{endpoint}/{api}/v{version}/',
                [
                    'scheme' => $this->scheme,
                    'endpoint' => $this->endpoint,
                    'api' => $this->api,
                    'version' => $this->version
                ]
            ],
            'defaults' => [
                'headers' => [
                    'Accept' => 'application/json'
                ]
            ]
        ]);
    }

    private function setStoragePath()
    {
        if (isset($this->storageDirectory) == false)
        {
            // The 'storage' directory is located on the same level as the 'src' directory,
            // we therefore want to go to the parent directory of the calling script (as it is in the 'src' directory),
            // and then down into the 'storage' directory.

            $scriptDirectory = preg_replace('~(\w)$~' , '$1' . DIRECTORY_SEPARATOR , realpath(getcwd()));
            $scriptParentDirectory = preg_replace( '~[/\\\\][^/\\\\]*[/\\\\]$~' , DIRECTORY_SEPARATOR , $scriptDirectory);
            $this->storageDirectory = $scriptParentDirectory . 'storage';
        }
    }

    // Resource: Token

    public function requestToken()
    {
        try
        {
            $response = $this->client->post('tokens', [
                'json' => [
                    'username' => $this->username,
                    'password' => $this->password
                ]
            ]);

            $data = $response->json();

            // Set token and token expiry
            $this->token = $data['data']['token'];
            $this->tokenExpiry = Carbon::createFromTimeStampUTC($data['notices'][0]['details']['expiry']['expires']);

            // Persist token and token expiry for future requests
            $this->storeToken();

            return true;
        }
        catch (ClientException $e)
        {
            $response = $e->getResponse();

            // Check if this was a credentials error
            if ($response->getStatusCode(401))
            {
                $data = $response->json();
                if ($data['errors'][0]['type'] == 'authentication_failure')
                {
                    echo "Invalid API credentials!" . " \n";
                    die();
                }
            }

            return false;
        }
    }

    private function storeToken()
    {
        $fileName = 'api_token.json';
        $filePath = $this->storageDirectory . DIRECTORY_SEPARATOR . $fileName;
        $fileContents = json_encode([
            'token' => $this->token,
            'token_expiry' => $this->tokenExpiry->timestamp
        ]);

        file_put_contents($filePath, $fileContents);
    }

    private function retrieveToken()
    {
        $fileName = 'api_token.json';
        $filePath = $this->storageDirectory . DIRECTORY_SEPARATOR . $fileName;
        $fileContents = file_get_contents($filePath);

        $fileContents = json_decode($fileContents, $associativeArray = true);

        // Set token and token expiry
        $this->token = $fileContents['token'];
        $this->tokenExpiry = Carbon::createFromTimeStampUTC($fileContents['token_expiry']);

        if ($this->checkTokenIsValid() === false)
        {
            // Un-set token and token expiry as they're invalid
            $this->token = null;
            $this->tokenExpiry = null;

            return false;
        }

        return true;
    }

    private function getToken()
    {
        // Check if there's a valid class instance token we can use
        if (isset($this->token) && isset($this->tokenExpiry))
        {
            return $this->checkTokenIsValid();
        }

        // Check if there's a valid persisted token we can use
        $retrieveToken = $this->retrieveToken();
        if ($retrieveToken === false)
        {
            return false;
        }

        return $this->checkTokenIsValid();
    }

    private function checkTokenIsValid()
    {
        // Ensure token hasn't already expired
        if (Carbon::now()->gte($this->tokenExpiry))
        {
            return false;
        }

        return $this->token;
    }

    protected function ensureValidToken()
    {
        // If a valid token already exists use it
        if ($this->getToken() !== false)
        {
            return true;
        }

        // Request a new token
        if ($this->requestToken() !== false)
        {
            return true;
        }

        // Unable to provide a valid token
        echo "Unable to get a valid token!" . " \n";
        die();
    }

    public function showToken()
    {
        $this->ensureValidToken();

        return $this->token;
    }
}
