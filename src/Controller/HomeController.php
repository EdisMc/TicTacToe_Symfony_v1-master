<?php

namespace App\Controller;

use AllowDynamicProperties;
use App\Service\MultiPlayerService;
use App\Service\SinglePlayerService;
use Error;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[AllowDynamicProperties] class HomeController extends AbstractController
{
    private SinglePlayerService $singlePlayerService;
    private MultiPlayerService $multiPlayerService;
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
        $this->singlePlayerService = new SinglePlayerService($requestStack);
        $this->multiPlayerService = new MultiPlayerService($requestStack);
    }
    #[Route('/', name: 'app_homepage')]
    public function homepage(): Response
    {
        return $this->render('tictac/home.html.twig');
    }

    #[Route('/', name: 'reset')]
    public function reset(): Response
    {
        $this->multiPlayerService->reset();
        return $this->render('tictac/homepage.html.twig');

    }

    #[Route('/remove-session', name: 'remove-game-session')]
    public function removeGameSession(): Response
    {
        if ($this->requestStack->getSession()->isStarted() && $this->requestStack->getSession()->has('single')) {
            $this->singlePlayerService->removeSession();
        } elseif ($this->requestStack->getSession()->isStarted() && $this->requestStack->getSession()->has('multi')) {
            $this->multiPlayerService->removeSession();
        } else {
            throw new Error('Session cannot be removed!');
        }

        return $this->redirectToRoute('app_homepage');
    }


}