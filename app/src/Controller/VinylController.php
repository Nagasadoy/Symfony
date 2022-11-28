<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Twig\Cache\CacheInterface;

class VinylController extends AbstractController
{
    #[Route('/vinyl', methods: ['GET'])]
    public function controller(HttpClientInterface $httpClient, CacheInterface $cache): Response
    {
        $mixes = $cache->get('mixes_data', function () use ($httpClient) {
            $response = $httpClient->request(
                'GET',
                'https://raw.githubusercontent.com/SymfonyCasts/vinyl-mixes/main/mixes.json'
            );

            $mixes = $response->toArray();
        });

        return  $this->json([
           'mixes' => $mixes
        ]);
    }
}