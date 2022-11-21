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
     * @return array|\ArrayObject|bool|float|int|string|void|null
     */
    public function normalize(mixed $object, string $format = null, array $context = [])
    {
        return [
            'id' => $object->getId()
        ];
    }

    public function supportsNormalization(mixed $data, string $format = null)
    {
        return $data instanceof Book;
    }
}