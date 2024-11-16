<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\LogicalCard;
use App\Entity\PhysicalCard;
use App\Exception\Data\LogicalCardNotFoundException;
use App\Repository\LogicalCardRepository;
use Symfony\Component\Workflow\Registry;

readonly class CardFactory
{
    public function __construct(
        private LogicalCardRepository $cardRepository,
        private Registry $registry,
    ) {
    }

    public function createCard(string $physicalCardId, string $logicalCardId): PhysicalCard
    {
        $logicalCard = $this->cardRepository->find($logicalCardId);
        if (! $logicalCard instanceof LogicalCard) {
            throw new LogicalCardNotFoundException($logicalCardId);
        }

        $classname = $this->getClassname($logicalCard->getSide()->value);

        /** @var PhysicalCard $card */
        $card = new $classname($physicalCardId, $logicalCard);
        $card->setTitle($logicalCard->getName());

        $this->registry->get($card)->getMarking($card);

        return $card;
    }

    private function getClassname(string $side): string
    {
        $classname = PhysicalCard::DISCRIMINATOR_MAP[$side] ?? null;

        if ($classname === null) {
            throw new \LogicException("Unknown card side {$side}");
        }

        return $classname;
    }
}
