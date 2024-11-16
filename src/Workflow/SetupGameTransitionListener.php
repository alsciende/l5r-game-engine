<?php

declare(strict_types=1);

namespace App\Workflow;

use App\Entity\Game;
use App\Entity\Player;
use Psr\Log\LoggerInterface;
use Symfony\Component\Workflow\Attribute\AsTransitionListener;
use Symfony\Component\Workflow\Event\TransitionEvent;

#[AsTransitionListener(workflow: 'game', transition: 'setup_game')]
readonly class SetupGameTransitionListener
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    /**
     * Setup Players
     * Determine First Player.
     */
    public function __invoke(TransitionEvent $event): void
    {
        /** @var Game $game */
        $game = $event->getSubject();

        $this->logger->debug('this is where we should setup the game', [
            'game_id' => $game->getId(),
        ]);

        foreach ($game->getPlayers() as $player) {
            $this->setup($player);
        }
    }

    /**
     * Shuffle decks
     * Gain Starting Honor
     * Fill Provinces
     * Draw Starting Hand.
     */
    private function setup(Player $player): void
    {
        $deck = $player->getDeck();
        $this->logger->debug('player', [
            'id' => $player->getId(),
        ]);

        foreach ($player->getPhysicalCards() as $physicalCard) {
            $this->logger->debug('card', [
                'id' => $physicalCard->getId(),
                'logical_card_id' => $physicalCard->getLogicalCard()->getId(),
                'place' => $physicalCard->getCurrentPlace(),
                'state' => $physicalCard->getState(),
            ]);
        }
    }
}
