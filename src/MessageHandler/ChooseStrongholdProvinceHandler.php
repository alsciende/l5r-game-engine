<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Enum\Type;
use App\Exception\Rules\WrongCardTypeException;
use App\Message\ChooseStrongholdProvince;
use App\Repository\CardRepository;
use App\Repository\GameRepository;
use App\Repository\PlayerRepository;
use App\Service\PlayerStateManager;
use App\State\PlayerState;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class ChooseStrongholdProvinceHandler
{
    public function __construct(
        private GameRepository $gameRepository,
        private PlayerRepository $playerRepository,
        private CardRepository $cardRepository,
        private PlayerStateManager $stateManager,
    ) {
    }

    public function __invoke(ChooseStrongholdProvince $message): void
    {
        $game = $this->gameRepository->get($message->getGameId(), 'place_provinces');
        $player = $this->playerRepository->get($message->getPlayerId(), $game);
        $card = $this->cardRepository->get($message->getCardId(), $game);
        $logicalCard = $card->getLogicalCard();

        if ($logicalCard->getType() !== Type::PROVINCE) {
            throw new WrongCardTypeException($card, Type::PROVINCE);
        }

        $this->stateManager->withState(
            $player,
            fn (PlayerState $state) => $state->setStrongholdProvinceId($card->getId()),
        );
    }
}
