<?php

namespace Satin\BASPeopleAPI\v1;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class PeopleAPI {

    private $scheme = 'http';

    private $endpoint = 'api.bas.ac.uk';

    private $api = 'people';

    private $version = 1;

    private $username;

    private $password;

    private $client;

    private $token;

    private $tokenExpiry;

    function __construct($username, $password)
    {
        // Set credentials
        $this->username = $username;
        $this->password = $password;

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
                    // TODO: Throw invalid credentials exception
                    echo "Invalid credentials exception!" . " \n";
                    die();
                }
            }

            return false;
        }
    }

    private function storeToken()
    {
        // TODO: Store token and token expiry to file
    }

    private function retrieveToken()
    {
        // TODO: Retrieve token from file
        // If token has expired return false even if token is set

        return false;
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

    private function ensureValidToken()
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
        // TODO: Raise no valid token exception
        echo "Unable to get valid token exception!" . " \n";
        die();
    }

    public function showToken()
    {
        $this->ensureValidToken();

        return $this->token;
    }

    // Resource: Person

    public function getPerson($reference)
    {
        // Method is authenticated so ensure we have a valid token
        $this->ensureValidToken();

        $response = $this->client->get(['people/{person_reference}', ['person_reference' => $reference]], [
            'headers' => [
                'Authorization' => 'Bearer' . ' ' . $this->token
            ]
        ]);

        $data = $response->json();

        return ($data['data']);
    }

    public function getEveryone()
    {
        // Method is authenticated so ensure we have a valid token
        $this->ensureValidToken();

        $response = $this->client->get('people', [
            'headers' => [
                'Authorization' => 'Bearer' . ' ' . $this->token
            ]
        ]);

        $data = $response->json();

        return ($data['data']);
    }
}
