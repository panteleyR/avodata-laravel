<?php

namespace App\Services;

use App\Contracts\MainMicroserviceConnector;
use \GuzzleHttp\Client;


class MainMicroservice implements MainMicroserviceConnector
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client(['base_uri' => env('MICROSERVICE_MAIN_URL') . '/api/v' . env('MICROSERVICE_MAIN_API_VERSION') . '/']);
    }

    public function getDomain(string $token): \stdClass
    {
        $response = $this->client->get('/services/main/get/domain', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json'
            ]
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('something went wrong');
        }

        $domain = json_decode($response->getBody()->getContents());

        if (!$domain) {
            throw new \Exception('dont found current domain');
        }

        if (!$domain->on) {
            throw new \Exception('domain inactive');
        }

        return $domain;
    }

    public function user(string $token): \stdClass
    {
        $response = $this->client->get('users/me', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json'
            ]
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('something went wrong');
        }

        $user = json_decode($response->getBody()->getContents());

        if (!$user) {
            throw new \Exception('dont found current user');
        }

        return $user;
    }

    public function getProviders(?string $token): array
    {
        if ($token) {
            $response = $this->client->get('/services/main/get/providers', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json'
                ]
            ]);
        } else {
            $response = $this->client->get('/services/main/get/providers', [
                'headers' => [
                    'Accept' => 'application/json'
                ]
            ]);
        }

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('something went wrong');
        }

        $providers = json_decode($response->getBody()->getContents());

        if (!$providers) {
            throw new \Exception('dont found current providers');
        }

        return $providers;
    }

    public function getCabinetDomains(string $token, int $cabinet_id): array
    {
        $response = $this->client->get('cabinets/' . $cabinet_id . '/domains', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json'
            ]
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('something went wrong');
        }

        $domains = json_decode($response->getBody()->getContents());

//        if (!$domains) {
//            throw new \Exception('dont found current domains');
//        }

        return $domains;
    }

    public function getDomains(string $token): array
    {
        $response = $this->client->get('domains', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json'
            ]
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('something went wrong');
        }

        $domains = json_decode($response->getBody()->getContents());

//        if (!$domains) {
//            throw new \Exception('dont found current domains');
//        }

        return $domains;
    }

    public function reduceDomainBalance(int $domainId)
    {
        $response = $this->client->post('/services/main/domains/' . $domainId . '/campaigns/current/reduce', [
            'headers' => [
                'Accept' => 'application/json'
            ]
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('something went wrong');
        }

        $contents = json_decode($response->getBody()->getContents());

        if (!$contents->message === 'ok') {
            throw new \Exception('error');
        }

        return $contents;
    }

    public function increaseDomainBalance(int $domainId, int $count)
    {
        $response = $this->client->post('/services/main/domains/' . $domainId . '/campaigns/current/increase', [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ],
            'json' => ['count' => $count]
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('something went wrong');
        }

        $contents = json_decode($response->getBody()->getContents());

        if (!$contents->message === 'ok') {
            throw new \Exception('error');
        }

        return $contents;
    }
}
