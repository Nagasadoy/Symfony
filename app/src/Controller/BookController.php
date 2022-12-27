<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Page;
use App\Repository\BookRepository;
use App\Services\BookService;
use App\Services\UploadedHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Sluggable\Util\Urlizer;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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

//    #[IsGranted('ROLE_ADMIN')]
    #[Route('/book', name: 'get_book_all', methods: ['GET'])]
    public function getAll(): Response
    {
//        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Доступ ограничен,
//            только админы могут посмотреть весь список');

        $books = $this->bookService->getAll();
        return $this->json(['books' => $books]);
    }

    #[Route('/book/all-ordered')]
    public function findAllOrdered(): Response
    {
        return $this->json($this->bookService->findAllOrdered());
    }

    #[Route('/book/like', methods: ['GET'])]
    public function bookLike(Request $request, BookRepository $bookRepository): Response
    {
        $content = $request->toArray();

        $bookLike = $content['name'];

        $books = $bookRepository->getBookLike($bookLike);
        return $this->json($books);
    }

    #[Route('/book/sum-pages/{id}', name: 'sum_pages', methods: ['GET'])]
    public function getSumPageNumberForBook(int $id): Response
    {
        $sum = $this->bookService->getSumPageNumberForBook($id);
        return $this->json([
            'sum_number_pages' => $sum,
            'bookId' => $id
        ]);
    }

    #[Route('/book/get-all-raw')]
    public function getAllRaw(): Response
    {
        return $this->json($this->bookService->getAllUsingPureSql());
    }

    #[Route('/book/statistic')]
    public function getStatisticByBook(): Response
    {
        $statistic = $this->bookService->getStatisticByBook();
        return $this->json($statistic);
    }

    #[Route('/book/{color}/color/{page}', methods: ['GET'])]
    public function boolEqualColor(int $page, string $color, BookRepository $bookRepository): Response
    {
//        $content = $request->toArray();
//        $maxPerPage = $content['maxPerPage'] ?? 10;

        $maxPerPage = 3;

        $queryBuilder = $bookRepository->createBookEqualColorQueryBuilder($color);
        $adapter = new QueryAdapter($queryBuilder);
        $data = Pagerfanta::createForCurrentPageWithMaxPerPage(
            $adapter,
            $page,
            $maxPerPage
        );

        return $this->json($data);
    }

    #[Route('/book/redblue', methods: ['GET'])]
    public function findRedOrBlueBooks(): Response
    {
        return $this->json($this->bookService->findRedOrBlueColorBooks());
    }

    #[Route('/book/{id}', name: 'get_book_by_id', methods: ['GET'])]
    public function getById(int $id): Response
    {
//        $book = $this->em->getRepository(Book::class)->find($id);
        $book = $this->bookService->findOne($id);
        return $this->json($book);
    }

    /**
     * Сохранение загруженного файла
     */
    #[Route('/book/upload', name: 'book_upload', methods: ['POST'])]
    public function uploadFile(
        Request        $request,
        BookRepository $bookRepository,
        UploadedHelper $uploadedHelper
    ): Response
    {
        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $request->files->get('image');
        if ($uploadedFile) {
            $newFileName = $uploadedHelper->uploadArticleImage($uploadedFile);

            /** @var Book $book */
            $book = $bookRepository->find(255);
            $book->setFileName($newFileName);
            $this->em->flush();
        }

        return $this->json(['status' => Response::HTTP_OK]);
    }

    #[Route('/upload-file', name: 'upload_file')]
    public function renderUploadFilePage(): Response
    {
        return $this->render('book/book.html.twig');
    }

    #[Route('api/book/create', name: 'book_create', methods: ['POST'])]
    public function createBook(Request $request): Response
    {
        try {
            $content = $request->toArray();
            $name = $content['name'];
            $newBook = new Book();
            $newBook->setName($name);

            $color = $content['color'] ?? 'red';
            $newBook->setColor($color);

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
                'date' => new \DateTimeImmutable()
            ], Response::HTTP_CREATED);
        } catch (\Throwable $ex) {
            return $this->json([
                'statusCode' => Response::HTTP_BAD_REQUEST
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/book/remove/{id}', name: 'book_remove', methods: ['DELETE'])]
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

    #[Route('/book/edit/{id}', name: 'book_edit', methods: ['PATCH'])]
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
