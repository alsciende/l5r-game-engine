<?php

declare(strict_types=1);

namespace App\Workflow;

use App\Entity\CardTypes\ConflictCard;
use App\Entity\CardTypes\DynastyCard;
use App\Entity\Game;
use App\Entity\Player;
use App\Exception\Data\DataException;
use App\Model\GroupedPlayerCards;
use App\Service\DeckShuffler;
use App\Service\PlayerStateManager;
use App\State\PlayerState;
use Psr\Log\LoggerInterface;
use Symfony\Component\Workflow\Attribute\AsTransitionListener;
use Symfony\Component\Workflow\Event\TransitionEvent;

#[AsTransitionListener(workflow: 'game', transition: 'setup_game')]
readonly class SetupGameTransitionListener
{
    public function __construct(
        private LoggerInterface $logger,
        private DeckShuffler $shuffler,
        private PlayerStateManager $stateManager,
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
        $placedCards = new GroupedPlayerCards($player->getPhysicalCards()->toArray());

        // shuffle decks
        // @TODO create and handle message to shuffle deck
        $this->shuffler->shuffleCards($placedCards->getCardsByPlace(DynastyCard::STATE_DRAW_DECK));
        $this->shuffler->shuffleCards($placedCards->getCardsByPlace(ConflictCard::STATE_DRAW_DECK));

        // gain starting honor
        // @TODO create and handle message to gain X honor
        $stronghold = $placedCards->getStrongholdLogicalCard();
        $honor = $stronghold->getHonor();
        if ($honor === null) {
            throw new DataException('Stronghold has no honor');
        }
        $this->stateManager->withState(
            $player,
            fn (PlayerState $state) => $state->setHonor($honor),
        );

        // fill provinces
        // @TODO create and handle message to put first card of Dynasty deck on given Province

        // draw starting hand
        // @TODO create and handle message to draw n=5 cards from Conflict deck to hand
    }
}
