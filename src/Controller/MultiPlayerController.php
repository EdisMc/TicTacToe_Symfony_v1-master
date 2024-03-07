<?php

namespace App\Controller;

use AllowDynamicProperties;
use App\Service\MultiPlayerService;
use Error;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[AllowDynamicProperties] class MultiPlayerController extends AbstractController
{
    private MultiPlayerService $multiPlayerService;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
        $this->multiPlayerService = new MultiPlayerService($requestStack);
    }

    #[Route('/multiplayer', name: 'app_multiplayer')]
    public function multiplayer(Request $request): Response
    {
        $data = $request->request->all();
        $selectedCell = $data['cell'] ?? null;

        try {
            if (is_array($selectedCell)) {
                $rowKeys = array_keys($selectedCell);
                $row = array_shift($rowKeys);

                $cellKeys = array_keys($_POST['cell'][$row]);
                $col = array_shift($cellKeys);

                $this->multiPlayerService->setPlayersMoves($row, $col);
            }
        } catch (Exception $exception) {
            throw new Error($exception);
        }

        $result = $this->multiPlayerService->getBoard();
        $showWinner = $this->multiPlayerService->showWinner();
        $status = $this->multiPlayerService->gameStatus();

        if (!$result) {
            throw $this->createNotFoundException('The page does not exist!');
        }

        return $this->render('tictac/multiplayer.html.twig', [
            'board' => $result,
            'winner' => $showWinner,
            'status' => $status
        ]);
    }


}