<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Page;
use App\Repository\BookRepository;
use App\Services\BookService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;

class BookController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $em, private readonly BookService $bookService)
    {
    }

    #[Route('/', name: 'get_book_all', methods: ['GET'])]
    public function getAll(): Response
    {
        $books = $this->bookService->getAll();
        return $this->json(['books' => $books]);
    }

    #[Route('/{id}', name: 'get_book_by_id', methods: ['GET'])]
    public function getById(int $id): Response
    {
        $book = $this->em->getRepository(Book::class)->find($id);
        return $this->json($book);
    }

    #[Route('/create', name: 'book_create', methods: ['POST'])]
    public function createBook(Request $request): Response
    {
        try {
            $content = $request->toArray();
            $name = $content['name'];
            $newBook = new Book();
            $newBook->setName($name);

            if (isset($content['pages']) && is_array($content['pages'])) {
                foreach ($content['pages'] as $pageData) {
                    $newBook->addPage($pageData['pageNumber'], $pageData['text']);
                }
            }

            $this->em->persist($newBook);
            $this->em->flush();

            return $this->json([
                'statusCode' => Response::HTTP_CREATED,
                'id' => $newBook->getId(),
                'date' => date("d.m.Y H:i:s")
            ], Response::HTTP_CREATED);
        } catch (\Throwable) {
            return $this->json([
                'statusCode' => Response::HTTP_BAD_REQUEST
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/remove/{id}', name: 'book_remove', methods: ['DELETE'])]
    public function removeBookById(int $id): Response
    {
        $book = $this->em->getRepository(Book::class)->find($id);

        if (!$book) {
            return $this->json([
                'statusCode' => Response::HTTP_NOT_FOUND
            ], Response::HTTP_NOT_FOUND);
        }

        $this->em->remove($book);
        $this->em->flush();

        return $this->json([
            'statusCode' => Response::HTTP_OK
        ]);
    }

    #[Route('edit/{id}', name: 'book_edit', methods: ['PATCH'])]
    public function editBook(Request $request, int $id): Response
    {
        $book = $this->em->getRepository(Book::class)->find($id);

        if (!$book) {
            return $this->json([
                'statusCode' => Response::HTTP_NOT_FOUND
            ], Response::HTTP_NOT_FOUND);
        }
        $content = $request->toArray();

        if (isset($content['name'])) {
            $book->setName($content['name']);
        }
        if (isset($content['pages'])) {
            try {
                $book->clearPages();
                foreach ($content['pages'] as $pageData) {
                    $book->addPage($pageData['pageNumber'], $pageData['text']);
                }
            } catch (\Throwable) {
                return $this->json([
                    'statusCode' => Response::HTTP_BAD_REQUEST,
                ], status: Response::HTTP_BAD_REQUEST);
            }
        }

        $this->em->flush();

        return $this->json([
            'statusCode' => Response::HTTP_OK,
            'book' => $book
        ]);
    }
}

// Если нужно выполнить что-то в одной транзакции, то можно написать вот так

//                $this->em->wrapInTransaction(function () use ($bookRepository, $id, $content, $book) {
//                    $bookRepository->removeAllPages($id);
//                    foreach ($content['pages'] as $pageData) {
//                        $book->addPage($pageData['pageNumber'], $pageData['text']);
//                    }
//                });
