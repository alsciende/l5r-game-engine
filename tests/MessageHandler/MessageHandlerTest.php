<?php

declare(strict_types=1);

namespace App\Tests\MessageHandler;

use App\DataFixtures\StarterCraneDeckTrait;
use App\DataFixtures\StarterDeckLionTrait;
use App\Entity\Event;
use App\Entity\Game;
use App\Entity\Player;
use App\Message\BidTokens;
use App\Message\CreateGame;
use App\Message\JoinGame;
use App\Repository\GameRepository;
use App\Service\StateManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Workflow\WorkflowInterface;

class MessageHandlerTest extends KernelTestCase
{
    use StarterDeckLionTrait;
    use StarterCraneDeckTrait;

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
            JoinGame::generateCardsIds($this->getLionStarterCards()),
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
            JoinGame::generateCardsIds($this->getCraneStarterCards()),
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
}
