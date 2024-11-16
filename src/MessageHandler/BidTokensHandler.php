<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Exception\Rules\InvalidTokenBidException;
use App\Message\BidTokens;
use App\Repository\GameRepository;
use App\Repository\PlayerRepository;
use App\Service\StateManager;
use App\State\Bid;
use App\State\State;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class BidTokensHandler
{
    public function __construct(
        private GameRepository $gameRepository,
        private PlayerRepository $playerRepository,
        private StateManager $stateManager,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(BidTokens $message): void
    {
        $game = $this->gameRepository->get($message->getGameId(), 'bid_tokens');
        $player = $this->playerRepository->get($message->getPlayerId(), $game);

        if ($message->getNbTokens() < 0) {
            throw new InvalidTokenBidException($message->getNbTokens());
        }

        $this->stateManager->withState(
            $game,
            fn (State $state): State => $state->addBid(new Bid($player->getId(), $message->getNbTokens())),
        );

        $this->logger->info("Player {$player->getId()} has bid {$message->getNbTokens()} tokens.");
    }
}
