<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Game;
use App\Repository\GameRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsCommand(
    name: 'app:game:transition',
    description: 'Add a short description for your command',
)]
class GameTransitionCommand extends Command
{
    public function __construct(
        private readonly GameRepository $repository,
        private readonly WorkflowInterface $gameStateMachine,
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this
            ->addArgument('game', InputArgument::REQUIRED, 'Game ID')
            ->addArgument('transition', InputArgument::REQUIRED, 'Transition name')
        ;
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /** @var string $gameId */
        $gameId = $input->getArgument('game');
        /** @var string $transitionName */
        $transitionName = $input->getArgument('transition');

        $game = $this->repository->find($gameId);

        if (! $game instanceof Game) {
            throw new \RuntimeException("Game {$gameId} not found.");
        }

        $this->gameStateMachine->apply($game, $transitionName);

        return Command::SUCCESS;
    }
}
