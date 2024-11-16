<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\LogicalCard;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LogicalCard>
 */
class LogicalCardRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, LogicalCard::class);
    }

    /**
     * @return bool Given an id, return true if the corresponding LogicalCard object exists, false otherwise
     */
    public function exists(LogicalCard $card): bool
    {
        return $this->find($card->getId()) !== null;
    }
}
