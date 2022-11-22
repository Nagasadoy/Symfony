<?php

namespace App\Services;

use App\Repository\BookRepository;

class BookService
{
    public function __construct(
        private readonly BookRepository $bookRepository,
        private readonly DymmyClass $dummy,
        private readonly TestInterface $test
    ) {
//        dump($this->dummy);
    }

    public function getAll(): array
    {
        return $this->bookRepository->findAll();
    }
}
