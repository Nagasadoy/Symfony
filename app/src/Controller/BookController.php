<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Page;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{

    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    #[Route('/', name: 'get_book_all', methods: ['GET'])]
    public function getAll(): Response
    {
        $books = $this->em->getRepository(Book::class)->findAll();
        foreach ($books as $book) {
            foreach ($book->getPages() as $page) {
                $p = $page;
            }
        }
        return $this->json([
            'books' => $books
        ]);
    }

    #[Route('/{id}', name: 'get_book_by_id', methods: ['GET'])]
    public function getById($id): Response
    {
        $book = $this->em->getRepository(Book::class)->find($id);
        return $this->json([
            'book' => $book
        ]);
    }

    #[Route('/create', name: 'book_create', methods: ['POST'])]
    public function createBook(Request $request): Response
    {
        try {
            $content = $request->toArray();
            $name = $content['name'];
            $newBook = new Book();
            $newBook->setName($name);

            $this->em->persist($newBook);
            $this->em->flush();

            if (isset($content['pages'])) {
                $bookPages = [];
                $pages = $content['pages'];
                foreach ($pages as $pageData) {
                    $page = new Page();
                    $page->setText($pageData['text']);
                    $page->setPageNumber($pageData['pageNumber']);
                    $page->setBook($newBook);
                    $this->em->persist($page);
                    $this->em->flush();
                    $bookPages[] = $page;
                }
                $newBook->setPages($bookPages);
                $this->em->persist($newBook);
                $this->em->flush();
            }

            return $this->json([
                'statusCode' => Response::HTTP_CREATED,
                'id' => $newBook->getId(),
                'date' => date("d.m.Y H:i:s")
            ]);
        } catch (\Throwable) {
            return $this->json([
                'statusCode' => Response::HTTP_BAD_REQUEST
            ]);
        }
    }

    #[Route('/remove/{id}', methods: ['DELETE'])]
    public function removeBookById($id): Response
    {
        $book = $this->em->getRepository(Book::class)->find($id);
        $this->em->remove($book);
        $this->em->flush();

        return $this->json([
           'statusCode' => Response::HTTP_OK
        ]);
    }
}