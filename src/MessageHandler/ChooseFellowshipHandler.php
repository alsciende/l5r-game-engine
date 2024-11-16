<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\CardTypes\CompanionCard;
use App\Exception\Rules\DuplicateStartingCompanionException;
use App\Exception\Rules\StartingFellowshipCardIsNotCompanionException;
use App\Exception\Rules\StartingTwilightCostTooHighException;
use App\Message\ChooseFellowship;
use App\Repository\CardRepository;
use App\Repository\GameRepository;
use App\Repository\PlayerRepository;
use App\Service\CardDataProvider;
use App\Service\StateManager;
use App\State\StartingFellowship;
use App\State\State;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsMessageHandler]
readonly class ChooseFellowshipHandler
{
    public function __construct(
        private GameRepository $gameRepository,
        private PlayerRepository $playerRepository,
        private StateManager $stateManager,
        private CardRepository $cardRepository,
        private CardDataProvider $dataProvider,
        private WorkflowInterface $companionCardStateMachine,
    ) {
    }

    public function __invoke(ChooseFellowship $message): void
    {
        $game = $this->gameRepository->get($message->getGameId(), 'choose_fellowship');
        $player = $this->playerRepository->get($message->getPlayerId(), $game);
        $cardIds = $message->getFellowship();
        $cards = [];

        $totalTwilightCost = 0;
        $uniqueCompanions = [];
        foreach ($cardIds as $cardId) {
            $card = $this->cardRepository->get($cardId, $game);
            if (! $card instanceof CompanionCard) {
                throw new StartingFellowshipCardIsNotCompanionException($card);
            }
            $cardData = $this->dataProvider->getCardData($card->getSource());
            if ($cardData->unique) {
                if (in_array($cardData->title, $uniqueCompanions, true)) {
                    throw new DuplicateStartingCompanionException($cardData->title);
                }
                $uniqueCompanions[] = $cardData->title;
            }
            $totalTwilightCost += $cardData->twilightCost;
            $cards[] = $card;
        }

        if ($totalTwilightCost > 4) {
            throw new StartingTwilightCostTooHighException($totalTwilightCost);
        }

        foreach ($cards as $card) {
            $this->companionCardStateMachine->apply($card, 'start_fellowship');
        }

        $this->stateManager->withState(
            $game,
            fn (State $state): State => $state->addStartingFellowship(
                new StartingFellowship($player->getId(), $message->getFellowship())
            ),
        );
    }
}
