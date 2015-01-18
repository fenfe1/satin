<?php

namespace Satin\BASPeopleAPI\v1;

use Satin\BASAPI\v1\BASAPIBase;

class PeopleAPI extends BASAPIBase {

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
