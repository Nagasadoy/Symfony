<?php

namespace App\Controller;

use Knp\Bundle\MarkdownBundle\MarkdownParserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class VinylController extends AbstractController
{
    #[Route('/vinyl', methods: ['GET'])]
    public function controller(
        HttpClientInterface $httpClient,
        CacheInterface $cache,
        MarkdownParserInterface $parser
    ): Response
    {

        $mixes = $cache->get('mixes_data', function () use ($httpClient) {
            $response = $httpClient->request(
                'GET',
                'https://raw.githubusercontent.com/SymfonyCasts/vinyl-mixes/main/mixes.json'
            );

            return $response->toArray();
        });

        $questionText = 'I\'ve been turned into a cat, any thoughts on how to turn back? While
            I\'m adorable, I don\'t really care for cat food.';

        $transformText = $parser->transformMarkdown($questionText);

        return $this->json([
            'mixes' => $mixes,
            'transformText' => $transformText
        ]);
    }
}