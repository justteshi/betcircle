<?php

declare(strict_types=1);

namespace App\Command\BetCircle;

use App\Entity\BetCircle\Fixture;
use App\Entity\BetCircle\GameWeek;
use App\Entity\BetCircle\League;
use App\Entity\BetCircle\Season;
use App\Entity\BetCircle\Team;
use App\Enum\BetCircle\FixtureStatus;
use App\Enum\BetCircle\GameWeekStatus;
use App\Enum\BetCircle\SeasonStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:betcircle:seed-dev-data',
    description: 'Seeds basic BetCircle development data.',
)]
final class SeedDevDataCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $existingLeague = $this->entityManager
            ->getRepository(League::class)
            ->findOneBy(['slug' => 'premier-league']);

        if (null !== $existingLeague) {
            $output->writeln('<comment>Dev data already seems to exist. Aborting.</comment>');

            return Command::SUCCESS;
        }

        $premierLeague = (new League())
            ->setName('Premier League')
            ->setSlug('premier-league')
            ->setCountry('England')
            ->setActive(true);

        $laLiga = (new League())
            ->setName('La Liga')
            ->setSlug('la-liga')
            ->setCountry('Spain')
            ->setActive(true);

        $this->entityManager->persist($premierLeague);
        $this->entityManager->persist($laLiga);

        $arsenal = (new Team())
            ->setName('Arsenal')
            ->setShortName('ARS')
            ->setSlug('arsenal')
            ->setCountry('England')
            ->setLeague($premierLeague)
            ->setActive(true);

        $chelsea = (new Team())
            ->setName('Chelsea')
            ->setShortName('CHE')
            ->setSlug('chelsea')
            ->setCountry('England')
            ->setLeague($premierLeague)
            ->setActive(true);

        $liverpool = (new Team())
            ->setName('Liverpool')
            ->setShortName('LIV')
            ->setSlug('liverpool')
            ->setCountry('England')
            ->setLeague($premierLeague)
            ->setActive(true);

        $realMadrid = (new Team())
            ->setName('Real Madrid')
            ->setShortName('RMA')
            ->setSlug('real-madrid')
            ->setCountry('Spain')
            ->setLeague($laLiga)
            ->setActive(true);

        $barcelona = (new Team())
            ->setName('Barcelona')
            ->setShortName('BAR')
            ->setSlug('barcelona')
            ->setCountry('Spain')
            ->setLeague($laLiga)
            ->setActive(true);

        $atleticoMadrid = (new Team())
            ->setName('Atletico Madrid')
            ->setShortName('ATM')
            ->setSlug('atletico-madrid')
            ->setCountry('Spain')
            ->setLeague($laLiga)
            ->setActive(true);

        foreach ([
                     $arsenal,
                     $chelsea,
                     $liverpool,
                     $realMadrid,
                     $barcelona,
                     $atleticoMadrid,
                 ] as $team) {
            $this->entityManager->persist($team);
        }

        $season = (new Season())
            ->setName('Spring Season 2026')
            ->setCode('SPRING_2026')
            ->setDescription('Development demo season')
            ->setStatus(SeasonStatus::ACTIVE)
            ->setStartDate(new \DateTimeImmutable('2026-03-01'))
            ->setEndDate(new \DateTimeImmutable('2026-06-01'))
            ->setVisible(true);

        $this->entityManager->persist($season);

        $week1 = (new GameWeek())
            ->setSeason($season)
            ->setName('Week 1')
            ->setStatus(GameWeekStatus::OPEN)
            ->setEntryCostTokens(10)
            ->setWeeklyPoolTokens(7)
            ->setSeasonalPoolContributionTokens(3)
            ->setFirstFixtureStartsAt(new \DateTimeImmutable('2026-03-14 15:00:00'))
            ->setJoinDeadlineAt(new \DateTimeImmutable('2026-03-14 13:00:00'))
            ->setPredictionLockAt(new \DateTimeImmutable('2026-03-14 14:00:00'))
            ->setVisible(true)
            ->setFinalized(false);

        $week2 = (new GameWeek())
            ->setSeason($season)
            ->setName('Week 2')
            ->setStatus(GameWeekStatus::OPEN)
            ->setEntryCostTokens(10)
            ->setWeeklyPoolTokens(7)
            ->setSeasonalPoolContributionTokens(3)
            ->setFirstFixtureStartsAt(new \DateTimeImmutable('2026-03-21 15:00:00'))
            ->setJoinDeadlineAt(new \DateTimeImmutable('2026-03-21 13:00:00'))
            ->setPredictionLockAt(new \DateTimeImmutable('2026-03-21 14:00:00'))
            ->setVisible(true)
            ->setFinalized(false);

        $this->entityManager->persist($week1);
        $this->entityManager->persist($week2);

        $fixtures = [
            (new Fixture())
                ->setGameWeek($week1)
                ->setHomeTeam($arsenal)
                ->setAwayTeam($chelsea)
                ->setDisplayOrder(1)
                ->setKickoffAt(new \DateTimeImmutable('2026-03-14 15:00:00'))
                ->setStatus(FixtureStatus::SCHEDULED),

            (new Fixture())
                ->setGameWeek($week1)
                ->setHomeTeam($realMadrid)
                ->setAwayTeam($barcelona)
                ->setDisplayOrder(2)
                ->setKickoffAt(new \DateTimeImmutable('2026-03-14 18:00:00'))
                ->setStatus(FixtureStatus::SCHEDULED),

            (new Fixture())
                ->setGameWeek($week2)
                ->setHomeTeam($liverpool)
                ->setAwayTeam($arsenal)
                ->setDisplayOrder(1)
                ->setKickoffAt(new \DateTimeImmutable('2026-03-21 15:00:00'))
                ->setStatus(FixtureStatus::SCHEDULED),

            (new Fixture())
                ->setGameWeek($week2)
                ->setHomeTeam($atleticoMadrid)
                ->setAwayTeam($barcelona)
                ->setDisplayOrder(2)
                ->setKickoffAt(new \DateTimeImmutable('2026-03-21 18:00:00'))
                ->setStatus(FixtureStatus::SCHEDULED),
        ];

        foreach ($fixtures as $fixture) {
            $this->entityManager->persist($fixture);
        }

        $this->entityManager->flush();

        $output->writeln('<info>BetCircle development data seeded successfully.</info>');

        return Command::SUCCESS;
    }
}
