<?php

namespace App\Services;

use App\Repository\BookRepository;
use Symfony\Component\Security\Core\Security;

class BookService
{
    public function __construct(
        private readonly BookRepository $bookRepository,
        private readonly DymmyClass $dummy,
        private readonly TestInterface $test,
        private readonly Security $security
    ) {
//        dump($this->dummy);
    }

    public function getAll(): array
    {
        echo $this->security->getUser()?->getUserIdentifier() ?? 'nobody'; // С помощью security можно получить доступ к
        // авторизованному пользователю
        return $this->bookRepository->findAll();
    }
}
