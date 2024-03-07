<?php

namespace App\Controller;

use AllowDynamicProperties;
use App\Service\SinglePlayerService;
use Error;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[AllowDynamicProperties] class SinglePlayerController extends AbstractController
{
    private SinglePlayerService $singlePlayerService;
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
        $this->singlePlayerService = new SinglePlayerService($requestStack);
    }

    #[Route('/computer', name: 'app_computer')]
    public function computer(Request $request): Response
    {
        $data = $request->request->all();
        $selectedCell = $data['cell'] ?? null;

        try {
            if (is_array($selectedCell)) {
                $rowKeys = array_keys($selectedCell);
                $row = array_shift($rowKeys);

                $cellKeys = array_keys($data['cell'][$row]);
                $col = array_shift($cellKeys);

                $this->singlePlayerService->getPlayerMove($row, $col);
                $this->singlePlayerService->setBotMoves($request);
            }
        } catch (Exception $exception) {
            throw new Error($exception);
        }

        $result = $this->singlePlayerService->getBoard();
        $winner = $this->singlePlayerService->showWinner();

        return $this->render('tictac/computer.html.twig', [
            'board' => $result,
            'winner' => $winner
        ]);
    }

}