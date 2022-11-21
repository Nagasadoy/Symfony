<?php

namespace App\Entity;

use App\Repository\PageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PageRepository::class)]
class Page
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $pageNumber = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $text = null;

    #[ORM\ManyToOne(targetEntity: Book::class, inversedBy: 'pages')]
    private Book $book;

    /**
     * @return Book
     */
    public function getBook(): Book
    {
        return $this->book;
    }

    /**
     * @param Book $book
     */
    public function setBook(Book $book): void
    {
        $this->book = $book;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPageNumber(): ?int
    {
        return $this->pageNumber;
    }

    public function setPageNumber(int $pageNumber): self
    {
        $this->pageNumber = $pageNumber;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }
}