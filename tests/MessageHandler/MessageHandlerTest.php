<?php

declare(strict_types=1);

namespace App\Tests\MessageHandler;

use App\DataFixtures\AppFixtures;
use App\Entity\Card;
use App\Entity\Event;
use App\Entity\Game;
use App\Entity\Player;
use App\Exception\Rules\DuplicateStartingCompanionException;
use App\Exception\Rules\StartingFellowshipCardIsNotCompanionException;
use App\Exception\Rules\StartingTwilightCostTooHighException;
use App\Message\BidTokens;
use App\Message\ChooseFellowship;
use App\Message\CreateGame;
use App\Message\JoinGame;
use App\Repository\CardRepository;
use App\Repository\GameRepository;
use App\Service\StateManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Workflow\WorkflowInterface;

class MessageHandlerTest extends KernelTestCase
{
    public function testCreateGameHandler(): string
    {
        $kernel = self::bootKernel();
        $container = static::getContainer();

        $gameId = Uuid::v4()->toString();

        /** @var MessageBusInterface $bus */
        $bus = $container->get(MessageBusInterface::class);

        $bus->dispatch(new CreateGame($gameId));

        /** @var EntityManagerInterface $manager */
        $manager = $container->get(EntityManagerInterface::class);

        /** @var Game $game */
        $game = $manager->getRepository(Game::class)->find($gameId);

        $this->assertInstanceOf(Game::class, $game);
        $this->assertSame('join_game', $game->getCurrentPlace());

        return $gameId;
    }

    /**
     * @depends testCreateGameHandler
     */
    public function testJoinGameHandler(string $gameId): string
    {
        $kernel = self::bootKernel();
        $container = static::getContainer();

        /** @var EntityManagerInterface $manager */
        $manager = $container->get(EntityManagerInterface::class);

        /** @var GameRepository $gameRepository */
        $gameRepository = $container->get(GameRepository::class);
        $game = $gameRepository->get($gameId);

        /** @var MessageBusInterface $bus */
        $bus = $container->get(MessageBusInterface::class);

        $playerId = Uuid::v4()->toString();
        $bus->dispatch(new JoinGame(
            $game->getId(),
            $playerId,
            'john',
            'John',
            '1',
            null,
            JoinGame::generateCardsIds(AppFixtures::ARAGON_STARTER_DECK),
        ));

        $john = $manager->getRepository(Player::class)->find($playerId);
        $this->assertInstanceOf(Player::class, $john);
        $this->assertSame($game->getId(), $john->getGame()?->getId());
        $this->assertSame(1, $game->getPlayers()->count());

        $playerId = Uuid::v4()->toString();
        $jane = $bus->dispatch(new JoinGame(
            $game->getId(),
            $playerId,
            'jane',
            'Jane',
            '1',
            null,
            JoinGame::generateCardsIds(AppFixtures::ARAGON_STARTER_DECK),
        ));

        $jane = $manager->getRepository(Player::class)->find($playerId);
        $this->assertInstanceOf(Player::class, $jane);
        $this->assertSame($game->getId(), $jane->getGame()?->getId());
        $this->assertSame(2, $game->getPlayers()->count());

        $this->assertSame('bid_tokens', $game->getCurrentPlace());

        return $gameId;
    }

    /**
     * @depends testJoinGameHandler
     */
    public function testBidTokensHandler(string $gameId): string
    {
        $kernel = self::bootKernel();
        $container = static::getContainer();

        /** @var GameRepository $gameRepository */
        $gameRepository = $container->get(GameRepository::class);
        $game = $gameRepository->get($gameId);
        $this->assertSame(2, $game->getPlayers()->count());
        $john = $game->getPlayers()->get(0);
        $this->assertInstanceOf(Player::class, $john);
        $jane = $game->getPlayers()->get(1);
        $this->assertInstanceOf(Player::class, $jane);

        // verify the initial place
        $this->assertSame('bid_tokens', $game->getCurrentPlace());

        /** @var EntityManagerInterface $manager */
        $manager = $container->get('doctrine.orm.entity_manager');

        /** @var WorkflowInterface $workflow */
        $workflow = $container->get('state_machine.game');
        $this->assertCount(0, $workflow->getEnabledTransitions($game));

        /** @var MessageBusInterface $bus */
        $bus = $container->get(MessageBusInterface::class);

        $bus->dispatch(new BidTokens($gameId, $john->getId(), 1));

        // verify place has not changed
        $this->assertSame('bid_tokens', $game->getCurrentPlace());

        $bus->dispatch(new BidTokens($gameId, $jane->getId(), 2));
        // verify the transition has been applied by GameEngine
        $this->assertSame('choose_fellowship', $game->getCurrentPlace());

        /** @var StateManager $stateManager */
        $stateManager = $container->get(StateManager::class);

        $this->assertCount(2, $stateManager->getState($game)->bids);

        $event = $manager->getRepository(Event::class)->findOneBy([], [
            'id' => 'desc',
        ]);
        $this->assertInstanceOf(Event::class, $event);
        $this->assertSame(BidTokens::class, $event->getName());

        return $gameId;
    }

    /**
     * @depends testJoinGameHandler
     */
    public function testChooseFellowshipInvalidCardHandler(string $gameId): string
    {
        $kernel = self::bootKernel();
        $container = static::getContainer();

        /** @var GameRepository $gameRepository */
        $gameRepository = $container->get(GameRepository::class);
        $game = $gameRepository->get($gameId);

        /** @var MessageBusInterface $bus */
        $bus = $container->get(MessageBusInterface::class);

        $player = $game->getPlayers()->get(0);
        $this->assertInstanceOf(Player::class, $player);

        /** @var CardRepository $cardRepository */
        $cardRepository = $container->get(CardRepository::class);

        $lurtz = $cardRepository->findOneBy([
            'game' => $game,
            'player' => $player,
            'source' => 1000127,
        ]);
        $this->assertInstanceOf(Card::class, $lurtz);

        $message = new ChooseFellowship(
            $gameId,
            $player->getId(),
            [$lurtz->getId()],
        );

        try {
            $bus->dispatch($message); // should throw
            $this->expectException(HandlerFailedException::class); // will always fail
        } catch (HandlerFailedException $exception) {
            $this->assertInstanceOf(StartingFellowshipCardIsNotCompanionException::class, $exception->getPrevious());
        }

        return $gameId;
    }

    /**
     * @depends testJoinGameHandler
     */
    public function testChooseFellowshipExcessiveCostHandler(string $gameId): string
    {
        $kernel = self::bootKernel();
        $container = static::getContainer();

        /** @var GameRepository $gameRepository */
        $gameRepository = $container->get(GameRepository::class);
        $game = $gameRepository->get($gameId);

        /** @var MessageBusInterface $bus */
        $bus = $container->get(MessageBusInterface::class);

        $player = $game->getPlayers()->get(0);
        $this->assertInstanceOf(Player::class, $player);

        /** @var CardRepository $cardRepository */
        $cardRepository = $container->get(CardRepository::class);

        $frodo = $cardRepository->findOneBy([
            'game' => $game,
            'player' => $player,
            'source' => 1000290,
        ]);
        $this->assertInstanceOf(Card::class, $frodo);

        $aragorn = $cardRepository->findOneBy([
            'game' => $game,
            'player' => $player,
            'source' => 1000365,
        ]);
        $this->assertInstanceOf(Card::class, $aragorn);

        $boromir = $cardRepository->findOneBy([
            'game' => $game,
            'player' => $player,
            'source' => 1000097,
        ]);
        $this->assertInstanceOf(Card::class, $boromir);

        $message = new ChooseFellowship(
            $gameId,
            $player->getId(),
            [$frodo->getId(), $aragorn->getId(), $boromir->getId()],
        );

        try {
            $bus->dispatch($message); // should throw
            $this->expectException(HandlerFailedException::class); // will always fail
        } catch (HandlerFailedException $exception) {
            $this->assertInstanceOf(StartingTwilightCostTooHighException::class, $exception->getPrevious());
        }

        return $gameId;
    }

    /**
     * @depends testJoinGameHandler
     */
    public function testChooseFellowshipDuplicateCompanionHandler(string $gameId): string
    {
        $kernel = self::bootKernel();
        $container = static::getContainer();

        /** @var GameRepository $gameRepository */
        $gameRepository = $container->get(GameRepository::class);
        $game = $gameRepository->get($gameId);

        /** @var MessageBusInterface $bus */
        $bus = $container->get(MessageBusInterface::class);

        $player = $game->getPlayers()->get(0);
        $this->assertInstanceOf(Player::class, $player);

        /** @var CardRepository $cardRepository */
        $cardRepository = $container->get(CardRepository::class);

        $frodo = $cardRepository->findOneBy([
            'game' => $game,
            'player' => $player,
            'source' => 1000290,
        ]);
        $this->assertInstanceOf(Card::class, $frodo);

        $aragorns = $cardRepository->findBy([
            'game' => $game,
            'player' => $player,
            'source' => 1000365,
        ]);
        $this->assertCount(2, $aragorns);

        $message = new ChooseFellowship(
            $gameId,
            $player->getId(),
            [$frodo->getId(), $aragorns[0]->getId(), $aragorns[1]->getId()],
        );

        try {
            $bus->dispatch($message); // should throw
            $this->expectException(HandlerFailedException::class); // will always fail
        } catch (HandlerFailedException $exception) {
            $this->assertInstanceOf(DuplicateStartingCompanionException::class, $exception->getPrevious());
        }

        return $gameId;
    }

    /**
     * @depends testChooseFellowshipDuplicateCompanionHandler
     */
    public function testChooseFellowshipHandler(string $gameId): string
    {
        $kernel = self::bootKernel();
        $container = static::getContainer();

        /** @var GameRepository $gameRepository */
        $gameRepository = $container->get(GameRepository::class);
        $game = $gameRepository->get($gameId);

        /** @var MessageBusInterface $bus */
        $bus = $container->get(MessageBusInterface::class);

        foreach ($game->getPlayers() as $index => $player) {
            $this->assertInstanceOf(Player::class, $player);

            /** @var CardRepository $cardRepository */
            $cardRepository = $container->get(CardRepository::class);

            // find cards
            $frodo = $cardRepository->findOneBy([
                'game' => $game,
                'player' => $player,
                'source' => 1000290,
            ]);
            $this->assertInstanceOf(Card::class, $frodo);
            $this->assertSame('draw_deck', $frodo->getCurrentPlace());

            $aragorn = $cardRepository->findOneBy([
                'game' => $game,
                'player' => $player,
                'source' => 1000365,
            ]);
            $this->assertInstanceOf(Card::class, $aragorn);
            $this->assertSame('draw_deck', $aragorn->getCurrentPlace());

            // create ChooseFellowship message with Frodo and Aragorn
            $message = new ChooseFellowship(
                $gameId,
                $player->getId(),
                [$frodo->getId(), $aragorn->getId()],
            );

            // dispatch message
            $bus->dispatch($message);

            // test game state has changed
            /** @var StateManager $stateManager */
            $stateManager = $container->get(StateManager::class);
            $this->assertCount($index + 1, $stateManager->getState($game)->startingFellowships);
            $this->assertSame('in_play', $frodo->getCurrentPlace());
            $this->assertSame('in_play', $aragorn->getCurrentPlace());
        }

        $this->assertSame('resolve_turn_start', $game->getCurrentPlace());

        return $gameId;
    }
}
