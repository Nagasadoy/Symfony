<?php

namespace App\Normalizer;

use App\Entity\Book;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class BookNormalizer implements NormalizerInterface
{
    /**
     * @param Book $object
     * @param string|null $format
     * @param array $context
     * @return array
     */
    public function normalize(mixed $object, string $format = null, array $context = []): array
    {
        return [
            'id' => $object->getId()
        ];
    }

    public function supportsNormalization(mixed $data, string $format = null): bool
    {
        return $data instanceof Book;
    }
}
