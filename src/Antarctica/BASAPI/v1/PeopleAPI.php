<?php

namespace Antarctica\BASAPI\v1;

use Antarctica\BASAPI\PeopleAPIInterface;

class PeopleAPI extends BASAPIBase implements PeopleAPIInterface {

    protected $api = 'people';

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
