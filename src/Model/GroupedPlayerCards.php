<?php

declare(strict_types=1);

namespace App\Model;

use App\Entity\LogicalCard;
use App\Entity\PhysicalCard;
use App\Enum\Type;
use App\Exception\Rules\DeckConstructionException;

class GroupedPlayerCards
{
    /**
     * @var array<string, list<PhysicalCard>>
     */
    private array $places = [];

    /**
     * @var array<string, list<PhysicalCard>>
     */
    private array $types = [];

    /**
     * @param list<PhysicalCard> $cards
     */
    public function __construct(array $cards)
    {
        foreach ($cards as $card) {
            $place = $card->getCurrentPlace();
            if ($place === null) {
                throw new \LogicException('Uninitialized card cannot be grouped');
            }

            if (! array_key_exists($place, $this->places)) {
                $this->places[$place] = [];
            }

            $this->places[$place][] = $card;

            $type = $card->getLogicalCard()->getType()->value;
            if (! array_key_exists($type, $this->types)) {
                $this->types[$type] = [];
            }

            $this->types[$type][] = $card;
        }
    }

    /**
     * @return array<string, list<PhysicalCard>>
     */
    public function getPlaces(): array
    {
        return $this->places;
    }

    /**
     * @return list<PhysicalCard>
     */
    public function getCardsByPlace(string $place): array
    {
        if (! array_key_exists($place, $this->places)) {
            return [];
        }

        return $this->places[$place];
    }

    /**
     * @return array<string, list<PhysicalCard>>
     */
    public function getTypes(): array
    {
        return $this->types;
    }

    /**
     * @return list<PhysicalCard>
     */
    public function getCardsByType(Type $type): array
    {
        if (! array_key_exists($type->value, $this->types)) {
            return [];
        }

        return $this->types[$type->value];
    }

    public function getStrongholdLogicalCard(): LogicalCard
    {
        $strongholds = $this->getCardsByType(Type::STRONGHOLD);

        if (count($strongholds) !== 1) {
            throw new DeckConstructionException('Deck must contain exactly one Stronghold');
        }

        return $strongholds[0]->getLogicalCard();
    }
}
