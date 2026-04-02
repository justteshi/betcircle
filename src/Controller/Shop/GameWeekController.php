<?php

declare(strict_types=1);

namespace App\Controller\Shop;

use App\Entity\BetCircle\GameWeek;
use App\Entity\BetCircle\Prediction;
use App\Entity\BetCircle\StandingSnapshot;
use App\Entity\BetCircle\WeekEntry;
use App\Enum\BetCircle\GameWeekStatus;
use App\Service\BetCircle\GameWeek\JoinWeekService;
use App\Service\BetCircle\Prediction\PredictionSubmissionService;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/gameweeks')]
final class GameWeekController extends AbstractController
{
    #[Route('', name: 'app_shop_gameweeks_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $weeks = $entityManager
            ->getRepository(GameWeek::class)
            ->findBy([], ['firstFixtureStartsAt' => 'ASC']);

        $joinedWeekIds = [];

        $user = $this->getUser();
        if ($user instanceof ShopUserInterface) {
            $customer = $user->getCustomer();

            if (null !== $customer) {
                $entries = $entityManager
                    ->getRepository(WeekEntry::class)
                    ->findBy(['customer' => $customer]);

                foreach ($entries as $entry) {
                    $gameWeek = $entry->getGameWeek();

                    if (null !== $gameWeek && null !== $gameWeek->getId()) {
                        $joinedWeekIds[] = $gameWeek->getId();
                    }
                }
            }
        }

        return $this->render('shop/gameweeks/index.html.twig', [
            'weeks' => $weeks,
            'joinedWeekIds' => $joinedWeekIds,
        ]);
    }

    #[Route('/{id}', name: 'app_shop_gameweeks_show', methods: ['GET'])]
    public function show(GameWeek $week, EntityManagerInterface $entityManager): Response
    {
        $weekEntry = null;
        $predictionsByFixtureId = [];

        $user = $this->getUser();
        if ($user instanceof ShopUserInterface) {
            $customer = $user->getCustomer();

            if (null !== $customer) {
                $weekEntry = $entityManager
                    ->getRepository(WeekEntry::class)
                    ->findOneBy([
                        'customer' => $customer,
                        'gameWeek' => $week,
                    ]);

                /** @var Prediction[] $predictions */
                $predictions = $entityManager
                    ->getRepository(Prediction::class)
                    ->findBy([
                        'customer' => $customer,
                        'gameWeek' => $week,
                    ]);

                foreach ($predictions as $prediction) {
                    $fixture = $prediction->getFixture();

                    if (null !== $fixture && null !== $fixture->getId()) {
                        $predictionsByFixtureId[$fixture->getId()] = $prediction;
                    }
                }
            }
        }

        $isWeekOpen = $week->getStatus() === GameWeekStatus::OPEN;
        $isWeekVisible = $week->isVisible();
        $joinClosed = $week->getJoinDeadlineAt() !== null && new \DateTimeImmutable() > $week->getJoinDeadlineAt();
        $predictionLocked = $week->getPredictionLockAt() !== null && new \DateTimeImmutable() > $week->getPredictionLockAt();

        return $this->render('shop/gameweeks/show.html.twig', [
            'week' => $week,
            'weekEntry' => $weekEntry,
            'fixtures' => $week->getFixtures(),
            'predictionsByFixtureId' => $predictionsByFixtureId,
            'isWeekOpen' => $isWeekOpen,
            'isWeekVisible' => $isWeekVisible,
            'joinClosed' => $joinClosed,
            'predictionLocked' => $predictionLocked,
        ]);
    }

    #[Route('/{id}/leaderboard', name: 'app_shop_gameweeks_leaderboard', methods: ['GET'])]
    public function leaderboard(GameWeek $week, EntityManagerInterface $entityManager): Response
    {
        /** @var array<int, StandingSnapshot> $leaderboard */
        $leaderboard = $entityManager
            ->getRepository(StandingSnapshot::class)
            ->findWeeklyByGameWeek($week);

        return $this->render('shop/gameweeks/leaderboard.html.twig', [
            'week' => $week,
            'leaderboard' => $leaderboard,
        ]);
    }

    #[Route('/{id}/join', name: 'app_shop_gameweeks_join', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function join(
        GameWeek $week,
        Request $request,
        JoinWeekService $joinWeekService,
    ): Response {
        if (!$this->isCsrfTokenValid('join_gameweek_'.$week->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Invalid CSRF token.');
        }

        /** @var ShopUserInterface $user */
        $user = $this->getUser();
        $customer = $user->getCustomer();

        if (null === $customer) {
            throw $this->createAccessDeniedException();
        }

        try {
            $joinWeekService->join($customer, $week);
            $this->addFlash('success', 'You successfully joined this Game Week.');
        } catch (\Throwable $exception) {
            $this->addFlash('error', $exception->getMessage());
        }

        return $this->redirectToRoute('app_shop_gameweeks_show', [
            'id' => $week->getId(),
            '_locale' => $request->getLocale(),
        ]);
    }

    #[Route('/{id}/predictions', name: 'app_shop_gameweeks_predictions_submit', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function submitPredictions(
        GameWeek $week,
        Request $request,
        PredictionSubmissionService $predictionSubmissionService,
    ): Response {
        if (!$this->isCsrfTokenValid('submit_predictions_'.$week->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Invalid CSRF token.');
        }

        /** @var ShopUserInterface $user */
        $user = $this->getUser();
        $customer = $user->getCustomer();

        if (null === $customer) {
            throw $this->createAccessDeniedException();
        }

        /** @var array<int, array{fixtureId?:mixed,predictedHomeScore?:mixed,predictedAwayScore?:mixed}> $predictionsData */
        $predictionsData = $request->request->all('predictions');

        try {
            $predictionSubmissionService->submit($customer, $week, $predictionsData);
            $this->addFlash('success', 'Predictions saved successfully.');
        } catch (\Throwable $exception) {
            $this->addFlash('error', $exception->getMessage());
        }

        return $this->redirectToRoute('app_shop_gameweeks_show', [
            'id' => $week->getId(),
            '_locale' => $request->getLocale(),
        ]);
    }
}
