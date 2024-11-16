<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Candidate;
use App\Entity\Deck;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:candidate:create',
    description: 'Create a Candidate',
)]
class CandidateCreateCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this
            ->addArgument('user', InputArgument::REQUIRED, 'User ID')
            ->addArgument('deck', InputArgument::REQUIRED, 'Deck ID')
        ;
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /** @var string $userId */
        $userId = $input->getArgument('user');
        /** @var string $deckId */
        $deckId = $input->getArgument('deck');

        $deck = $this->entityManager->find(Deck::class, $deckId);
        if (! $deck instanceof Deck) {
            throw new \RuntimeException("Deck {$deckId} not found.");
        }

        $candidate = new Candidate();
        $candidate->setUserId($userId);
        $candidate->setDeck($deck);
        $this->entityManager->persist($candidate);
        $this->entityManager->flush();

        $io->success("Candidate {$candidate->getId()} created.");

        return Command::SUCCESS;
    }
}
