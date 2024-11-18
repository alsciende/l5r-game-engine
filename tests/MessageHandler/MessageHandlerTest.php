<?php

declare(strict_types=1);

namespace App\Tests\MessageHandler;

use App\DataFixtures\StarterCraneDeckTrait;
use App\DataFixtures\StarterDeckLionTrait;
use App\Entity\CardTypes\ConflictCard;
use App\Entity\CardTypes\DynastyCard;
use App\Entity\Game;
use App\Entity\PhysicalCard;
use App\Entity\Player;
use App\Message\ChooseStrongholdProvince;
use App\Message\CreateGame;
use App\Message\JoinGame;
use App\Repository\CardRepository;
use App\Repository\GameRepository;
use App\Service\PlayerStateManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;

class MessageHandlerTest extends KernelTestCase
{
    use StarterDeckLionTrait;
    use StarterCraneDeckTrait;

    protected function setUp(): void
    {
        self::bootKernel();
    }

    public function testCreateGameHandler(): string
    {
        $container = static::getContainer();

        $gameId = Uuid::v4()->toString();

        /** @var MessageBusInterface $bus */
        $bus = $container->get(MessageBusInterface::class);

        $message = new CreateGame($gameId);
        $bus->dispatch($message);

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
        $container = static::getContainer();

        /** @var EntityManagerInterface $manager */
        $manager = $container->get(EntityManagerInterface::class);

        /** @var GameRepository $gameRepository */
        $gameRepository = $container->get(GameRepository::class);
        $game = $gameRepository->get($gameId);

        /** @var MessageBusInterface $bus */
        $bus = $container->get(MessageBusInterface::class);

        $playerId = Uuid::v4()->toString();
        $message = new JoinGame(
            $game->getId(),
            $playerId,
            'john',
            'John',
            '1',
            null,
            JoinGame::generateCardsIds($this->getLionStarterCards()),
        );
        $bus->dispatch($message);

        $john = $manager->getRepository(Player::class)->find($playerId);
        $this->assertInstanceOf(Player::class, $john);
        $this->assertSame($game->getId(), $john->getGame()->getId());
        $this->assertSame(1, $game->getPlayers()->count());

        $playerId = Uuid::v4()->toString();
        $message = new JoinGame(
            $game->getId(),
            $playerId,
            'jane',
            'Jane',
            '1',
            null,
            JoinGame::generateCardsIds($this->getCraneStarterCards()),
        );

        $jane = $bus->dispatch($message);

        $jane = $manager->getRepository(Player::class)->find($playerId);
        $this->assertInstanceOf(Player::class, $jane);
        $this->assertSame($game->getId(), $jane->getGame()->getId());
        $this->assertSame(2, $game->getPlayers()->count());
        $this->assertSame(52, $john->getPhysicalCards()->count());
        $this->assertSame(52, $jane->getPhysicalCards()->count());

        $this->assertSame('place_provinces', $game->getCurrentPlace());

        return $gameId;
    }

    /**
     * @depends testJoinGameHandler
     */
    public function testChooseStrongholdProvinceHandler(string $gameId): string
    {
        $container = static::getContainer();

        /** @var EntityManagerInterface $manager */
        $manager = $container->get(EntityManagerInterface::class);

        /** @var GameRepository $gameRepository */
        $gameRepository = $container->get(GameRepository::class);
        $game = $gameRepository->get($gameId);
        $this->assertSame(2, $game->getPlayers()->count());
        $john = $game->getPlayers()->get(0);
        $this->assertInstanceOf(Player::class, $john);
        $this->assertSame(52, $john->getPhysicalCards()->count());
        $jane = $game->getPlayers()->get(1);
        $this->assertInstanceOf(Player::class, $jane);
        $this->assertSame(52, $jane->getPhysicalCards()->count());

        /** @var MessageBusInterface $bus */
        $bus = $container->get(MessageBusInterface::class);

        /** @var CardRepository $cardRepository */
        $cardRepository = $container->get(CardRepository::class);

        /** @var PlayerStateManager $playerStateManager */
        $playerStateManager = $container->get(PlayerStateManager::class);

        $province = $cardRepository->findOneBy([
            'game' => $game,
            'player' => $john,
            'title' => 'Ancestral Lands',
        ]);
        $this->assertInstanceOf(PhysicalCard::class, $province);

        $message = new ChooseStrongholdProvince(
            $gameId,
            $john->getId(),
            $province->getId()
        );

        $bus->dispatch($message);
        $this->assertSame(52, $john->getPhysicalCards()->count());
        $this->assertSame(52, $jane->getPhysicalCards()->count());

        $this->assertSame('place_provinces', $game->getCurrentPlace());

        $province = $cardRepository->findOneBy([
            'game' => $game,
            'player' => $jane,
            'title' => 'Entrenched Position',
        ]);
        $this->assertInstanceOf(PhysicalCard::class, $province);

        $message = new ChooseStrongholdProvince(
            $gameId,
            $jane->getId(),
            $province->getId()
        );

        $bus->dispatch($message);

        $this->assertSame('dynasty_phase_begins', $game->getCurrentPlace());

        $dynastyPositions = array_map(
            fn (PhysicalCard $card) => $card->getPosition(),
            $john->getCardsByPlace(DynastyCard::STATE_DRAW_DECK)
        );
        $this->assertSame(count($dynastyPositions), count(array_unique($dynastyPositions)));
        $this->assertSame(19, count($john->getCardsByPlace(ConflictCard::STATE_DRAW_DECK)));
        $this->assertSame(12, $playerStateManager->getState($john)->getHonor());

        return $gameId;
    }
}
