<?php

namespace App\Repository;

use App\Entity\Link;
use App\Repository\Exception\LinkNotFoundException;
use App\Service\UrlHasher;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Throwable;

/**
 * @method Link|null find($id, $lockMode = null, $lockVersion = null)
 * @method Link|null findOneBy(array $criteria, array $orderBy = null)
 * @method Link[]    findAll()
 * @method Link[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LinkRepository extends ServiceEntityRepository
{
    public function __construct(
        private UrlHasher $urlHasher,
        ManagerRegistry $registry
    ) {
        parent::__construct($registry, Link::class);
    }

    public function add(Link $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function remove(Link $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws LinkNotFoundException
     */
    public function resolve(string $shortCode): Link
    {
        $link = $this->findOneBy([
            'customShortcode' => $shortCode
        ]);

        if ($link instanceof Link) {
            return $link;
        }

        try {
            $linkId = $this->urlHasher->getHasher()->decode($shortCode)[0];

            $link = $this->find($linkId);

            if ($link instanceof Link) {
                return $link;
            }
        } catch (Throwable) {}

        throw LinkNotFoundException::createFromShortcode($shortCode);
    }
}
