<?php

namespace App\Repository;

use App\Entity\Note;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Note|null find($id, $lockMode = null, $lockVersion = null)
 * @method Note|null findOneBy(array $criteria, array $orderBy = null)
 * @method Note[]    findAll()
 * @method Note[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Note::class);
    }

    /**
     * @param int $author_id
     * @return Note[] Returns an array of Note objects
     */
    public function getUserNotes(int $author_id)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('IDENTITY(n.author) = :author_id')
            ->setParameter('author_id', $author_id)
            ->orderBy('n.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }

}
