<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Card;
use App\Service\CardDataProvider;
use Symfony\Component\Workflow\Registry;

readonly class CardFactory
{
    public function __construct(
        private CardDataProvider $provider,
        private Registry $registry,
    ) {
    }

    public function createCard(string $cardId, int $source): Card
    {
        $cardData = $this->provider->getCardData($source);

        $classname = $this->getClassname($cardData->type);

        /** @var Card $card */
        $card = new $classname($cardId, $source);
        $card->setImage($cardData->image);
        $card->setTitle($cardData->title);
        $card->setSubtitle($cardData->subtitle);

        $this->registry->get($card)->getMarking($card);

        return $card;
    }

    private function getClassname(string $type): string
    {
        $classname = Card::DISCRIMINATOR_MAP[$type] ?? null;

        if ($classname === null) {
            throw new \LogicException("Unknown card type {$type}");
        }

        return $classname;
    }
}
