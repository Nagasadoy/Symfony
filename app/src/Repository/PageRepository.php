<?php

namespace App\Repository;

use App\Entity\Page;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Page>
 *
 * @method Page|null find($id, $lockMode = null, $lockVersion = null)
 * @method Page|null findOneBy(array $criteria, array $orderBy = null)
 * @method Page[]    findAll()
 * @method Page[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Page::class);
    }

    public function save(Page $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Page $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function removeByBook(int $bookId): void
    {
        // Элиас p для текущей таблицы
        $this->createQueryBuilder('p')
            ->delete()
            ->where('p.book = :bookId')
            ->setParameter('bookId', $bookId)
            ->getQuery()
            ->execute();

        // Но через entity manager мы можем работать с любой таблицей, но тогда нужно использовать метод from
//        $this->getEntityManager()->createQueryBuilder('p')
//            ->from(Page::class, 'p')
//            ->delete()
//            ->where('p.book = :bookId')
//            ->setParameter('bookId', $bookId)
//            ->getQuery()
//            ->execute();
    }
}
