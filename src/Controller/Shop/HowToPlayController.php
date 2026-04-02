<?php

declare(strict_types=1);

namespace App\Controller\Shop;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HowToPlayController extends AbstractController
{
    #[Route('/how-to-play', name: 'app_shop_how_to_play', methods: ['GET'])]
    public function __invoke(): Response
    {
        return $this->render('shop/how_to_play/index.html.twig');
    }
}
