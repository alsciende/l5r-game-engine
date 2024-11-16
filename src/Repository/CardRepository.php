<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Card;
use App\Entity\Game;
use App\Exception\Data\CardNotFoundException;
use App\Exception\Data\GameMismatchException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Card>
 *
 * @method Card|null find($id, $lockMode = null, $lockVersion = null)
 * @method Card|null findOneBy(array $criteria, array $orderBy = null)
 * @method Card[]    findAll()
 * @method Card[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Card::class);
    }

    public function get(string $cardId, ?Game $game = null): Card
    {
        $card = $this->find($cardId);

        if ($card === null) {
            throw new CardNotFoundException($cardId);
        }

        if ($game instanceof Game && $card->getGame()?->getId() !== $game->getId()) {
            throw new GameMismatchException();
        }

        return $card;
    }

    //    /**
    //     * @return Card[] Returns an array of Card objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Card
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
