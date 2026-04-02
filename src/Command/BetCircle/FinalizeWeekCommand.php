<?php

declare(strict_types=1);

namespace App\Command\BetCircle;

use App\Entity\BetCircle\GameWeek;
use App\Service\BetCircle\GameWeek\WeekFinalizationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:betcircle:finalize-week',
    description: 'Finalize a GameWeek (score, leaderboard, payouts)'
)]
final class FinalizeWeekCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly WeekFinalizationService $weekFinalizationService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('id', InputArgument::REQUIRED, 'GameWeek ID');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $id = (int) $input->getArgument('id');

        /** @var GameWeek|null $gameWeek */
        $gameWeek = $this->entityManager
            ->getRepository(GameWeek::class)
            ->find($id);

        if (null === $gameWeek) {
            $output->writeln('<error>GameWeek not found.</error>');
            return Command::FAILURE;
        }

        try {
            $this->weekFinalizationService->finalize($gameWeek);

            $output->writeln(sprintf(
                '<info>GameWeek "%s" finalized successfully.</info>',
                $gameWeek->getName()
            ));

            return Command::SUCCESS;

        } catch (\Throwable $exception) {
            $output->writeln('<error>'.$exception->getMessage().'</error>');

            return Command::FAILURE;
        }
    }
}
