<?php

declare(strict_types=1);

namespace App\Command;

use App\Message\CreateGame;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;

#[AsCommand(
    name: 'app:game:create',
    description: 'Create a game',
)]
class GameCreateCommand extends Command
{
    public function __construct(
        private readonly MessageBusInterface $bus,
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $gameId = Uuid::v4()->toString();
        $this->bus->dispatch(new CreateGame($gameId));

        $io->success("Game {$gameId} created successfully.");

        return Command::SUCCESS;
    }
}
