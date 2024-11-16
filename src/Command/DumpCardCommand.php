<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\CardDataProvider;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:dump:card',
    description: 'Add a short description for your command',
)]
class DumpCardCommand extends Command
{
    public function __construct(
        private readonly CardDataProvider $cardManager,
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this
            ->addArgument('card', InputArgument::REQUIRED, 'Card id')
            ->addArgument('card', InputArgument::REQUIRED, 'Card id')
        ;
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $cardId = $input->getArgument('card');
        if (is_numeric($cardId) === false) {
            throw new \RuntimeException('Numeric expected');
        }
        $io->comment('Searching for card ' . $cardId);

        $cardData = $this->cardManager->getCardData((int) $cardId);
        var_dump($cardData);

        return Command::SUCCESS;
    }
}
