<?php

namespace App\Services;

use App\Normalizer\BookNormalizer;
use App\Repository\PageRepository;

class DymmyClass
{
    private int $c;
    public string $d;

    public function __construct(private int $a, private string $b, private bool $isDebug)
    {
    }

    public function getA(): int
    {
        return $this->a;
    }

    public function getB(): int
    {
        return $this->b;
    }

    public function todo(): void
    {
        $c = $this->a + (int)$this->b;
        echo $c;
    }

    public function setC(int $value): void
    {
        $this->c = $value;
    }
}
