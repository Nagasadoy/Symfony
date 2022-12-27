<?php

namespace App\Repository;

use App\Entity\Book;
use App\Entity\Page;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 *
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);

        $filters = $this->getEntityManager()->getFilters()->enable('book_color_filter');
        $filters->setParameter('color', 'red');
    }

    public function save(Book $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Book $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAllOrdered(): array
    {
        $dql = 'SELECT book FROM App\Entity\Book book ORDER BY book.name DESC';
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->execute();
    }

    public function getBookLike(string $likeName): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.name LIKE :likeName')
            ->setParameter('likeName', '%' . $likeName . '%')
            ->getQuery()
            ->getResult();
    }

    public function createBookEqualColorQueryBuilder(string $color): QueryBuilder
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.color = :colorName')
            ->setParameter('colorName', $color);
    }

    public function findOneBookWithPages(int $bookId): ?Book
    {
//        $book = $this->createQueryBuilder('b')
//            ->leftJoin('b.pages', 'p')
////            ->addSelect('p') // Если мы хотим сделать всё за 1 запрос, иначе будет ленивая загрузка
//            ->andWhere('b.id = :bookId')
//            ->setParameter('bookId', $bookId)
//            ->getQuery()
//            ->getOneOrNullResult();

        $qb = $this->createQueryBuilder('b');

        $this->addPages($qb);

        $book = $qb->andWhere('b.id = :bookId')
            ->setParameter('bookId', $bookId)
            ->getQuery()
            ->getOneOrNullResult();

        return $book;
    }

    public function findSumNumberPagesForBook(int $bookId): int
    {
        $sum = $this->createQueryBuilder('b')
            ->leftJoin('b.pages', 'p')
            ->andWhere('b.id = :bookId')
            ->setParameter('bookId', $bookId)
            ->select('SUM(p.pageNumber) as pageNumber')
            ->getQuery()
            ->getSingleScalarResult();
//        dd($sum);
        return $sum;
    }

    /**
     * @return Book[]
     */
    public function findRedOrBlueColorBooks(): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.color = :red OR b.color = :blue')
            ->setParameter('red', 'red')
            ->setParameter('blue', 'blue')
            ->getQuery()
            ->getResult();
    }

    public function getStatisticByBook(int $bookId = 1): array
    {
        return $this->createQueryBuilder('b')
            ->leftJoin('b.pages', 'p')
            ->andWhere('b.id = :bookId')
            ->setParameter('bookId', $bookId)
            ->select(
                'SUM(p.pageNumber) as sumPage,
                 AVG(p.pageNumber) as avgPage,
                 MAX(p.pageNumber) as maxPage
            ')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getAllUsingPureSql(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "SELECT a.* FROm book a INNER JOIN page b ON a.id=b.book_id WHERE a.id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, 1);
        $result = $stmt->executeQuery();
        return $result->fetchAllAssociative();
    }

    private function addPages(QueryBuilder $queryBuilder): QueryBuilder
    {
        return $queryBuilder->leftJoin('b.pages', 'p')
            ->addSelect('p');
    }
}
