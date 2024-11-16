<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Event;
use App\Entity\Game;
use App\Repository\EventRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:event:list',
    description: 'List the last N events',
)]
class EventListCommand extends Command
{
    public function __construct(
        private readonly EventRepository $repository,
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this
            ->addOption('lines', 'l', InputOption::VALUE_REQUIRED, 'Number of lines', '10')
//            ->addOption('game', 'g', InputOption::VALUE_REQUIRED, 'Game ID')
        ;
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $lines = $input->getOption('lines');
        if (! is_numeric($lines)) {
            throw new \RuntimeException('Option --lines must be numeric.');
        }
        $limit = intval($lines);

        $events = $this->repository->findBy([], [
            'id' => 'desc',
        ], $limit);

        $io->table(
            ['Id', 'Name', 'Payload'],
            array_map(
                fn (Event $event): array => [$event->getId(), $event->getName(), $event->getPayload()],
                $events
            )
        );

        return Command::SUCCESS;
    }
}
