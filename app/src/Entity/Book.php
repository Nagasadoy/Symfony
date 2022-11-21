<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookRepository::class)]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /** @var Collection */
    #[ORM\OneToMany(mappedBy: 'book', targetEntity: Page::class, cascade: ['remove', 'persist'])]
    private $pages;

    public function __construct()
    {
        $this->pages = new ArrayCollection();
    }


    public function getPages(): Collection
    {
        return $this->pages;
    }


    /**
     * @param Page[] $pages
     * @return void
     */
    public function setPages(array $pages): void
    {
        foreach ($pages as $page) {
            $page->setBook($this);
        }
        $this->pages = $pages;
    }

    public function addPage(int $pageNumber, string $pageText): void
    {
        $page = new Page();
        $page->setBook($this);
        $page->setPageNumber($pageNumber);
        $page->setText($pageText);
        $this->pages->add($page);
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

}
