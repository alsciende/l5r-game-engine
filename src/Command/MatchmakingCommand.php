<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\MatchMaking\MatchMakingInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:matchmaking',
    description: 'Launch Matchmaking on the current Candidates',
)]
class MatchmakingCommand extends Command
{
    public function __construct(
        private readonly MatchMakingInterface $matchMaking,
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

        $this->matchMaking->findMatches();

        return Command::SUCCESS;
    }
}
