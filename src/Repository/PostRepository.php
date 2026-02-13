<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Post>
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function findAllPublished(): array
    {
        $now = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
        return $this->createQueryBuilder('p')
            ->andWhere('p.isPublished = :published')
            ->andWhere('p.publishedAt <= :now')
            ->setParameter('published', true)
            ->setParameter('now', $now)
            ->orderBy('p.publishedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByCategory(int $categoryId): array
    {
        $now = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
        return $this->createQueryBuilder('p')
            ->andWhere('p.category = :categoryId')
            ->andWhere('p.isPublished = :published')
            ->andWhere('p.publishedAt <= :now')
            ->setParameter('categoryId', $categoryId)
            ->setParameter('published', true)
            ->setParameter('now', $now)
            ->orderBy('p.publishedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find posts that should be automatically published
     * (published date has passed but still marked as unpublished)
     */
    public function findScheduledForPublication(): array
    {
        $now = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
        return $this->createQueryBuilder('p')
            ->andWhere('p.isPublished = :published')
            ->andWhere('p.publishedAt <= :now')
            ->setParameter('published', false)
            ->setParameter('now', $now)
            ->orderBy('p.publishedAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
