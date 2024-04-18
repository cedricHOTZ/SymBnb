<?php

namespace App\Service;

use GuzzleHttp\Client;

class UnsplashService
{
    private $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.unsplash.com/',
        ]);
    }

    public function getRandomImageUrls($count = 5)
    {
        $response = $this->client->request('GET', 'photos/random', [
            'query' => [
                'client_id' => '90jbpGzJJ8yJyYZgTiAzkUzX2KqAV6yyU_Re9SKD4CU',
                'count' => $count,
            ],
        ]);

        if ($response->getStatusCode() === 200) {
            $data = json_decode($response->getBody()->getContents(), true);
            $imageUrls = [];

            foreach ($data as $item) {
                $imageUrls[] = $item['urls']['regular'];
            }

            return $imageUrls;
        }

        return [];
    }
}
