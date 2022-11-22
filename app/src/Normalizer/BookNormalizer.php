<?php

namespace App\Normalizer;

use App\Entity\Book;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class BookNormalizer implements NormalizerInterface
{
    public function __construct(/*private string $param*/)
    {
    }

    /**
     * @param Book $object
     * @param string|null $format
     * @param array $context
     * @return array
     */
    public function normalize(mixed $object, string $format = null, array $context = []): array
    {
        return [
            'id' => $object->getId() // $this->param это посмотреть как создаются через di
        ];
    }

    public function supportsNormalization(mixed $data, string $format = null): bool
    {
        return $data instanceof Book;
    }
}
