<?php

namespace App\Services;

use App\Entity\Book;
use App\Repository\BookRepository;
use Symfony\Component\Security\Core\Security;

class BookService
{
    public function __construct(
        private readonly BookRepository $bookRepository,
        private readonly DymmyClass     $dummy,
        private readonly TestInterface  $test,
        private readonly Security       $security
    )
    {
//        dump($this->dummy);
    }

    public function getAll(): array
    {
        echo $this->security->getUser()?->getUserIdentifier() ?? 'nobody'; // С помощью security можно получить доступ к
        // авторизованному пользователю
        return $this->bookRepository->findAll();
    }

    public function findAllOrdered(): array
    {
        return $this->bookRepository->findAllOrdered();
    }

    public function findOne(int $bookId): ?Book
    {
        return $this->bookRepository->findOneBookWithPages($bookId);
    }

    /**
     * @return Book[]
     */
    public function findRedOrBlueColorBooks(): array
    {
        return $this->bookRepository->findRedOrBlueColorBooks();
    }

    public function getSumPageNumberForBook(int $bookId): int
    {
        return $this->bookRepository->findSumNumberPagesForBook($bookId);
    }

    public function getStatisticByBook(): array
    {
        return $this->bookRepository->getStatisticByBook();
    }

    public function getAllUsingPureSql(): array
    {
        return $this->bookRepository->getAllUsingPureSql();
    }

}
