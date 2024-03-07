<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class SinglePlayerService extends GameService
{
    const SINGLEPLAYER_SESSION = 'single';
    public function __construct(private readonly RequestStack $requestStack)
    {
        if (
            $this->requestStack->getCurrentRequest()
            && $this->requestStack->getCurrentRequest()->getSession()
            && $this->requestStack->getCurrentRequest()->getSession()->has(self::SINGLEPLAYER_SESSION)
            && $this->requestStack->getCurrentRequest()->getSession()->get(self::SINGLEPLAYER_SESSION, $this->getBoard())
        ) {
            $data = $this->requestStack->getCurrentRequest()->getSession()->get(self::SINGLEPLAYER_SESSION);
            $this->player = $data['player'];
            $this->board = $data['board'];

        } else {
            $this->board = array_fill(0, 3, array_fill(0, 3, null));
            $this->player = "X";
        }
    }

    public function getPlayerMove($row, $col): void
    {
        if ($this->board[$row][$col] == null) {
            $this->board[$row][$col] = $this->player;
        }
    }

    public function setBotMoves(Request $request): void
    {
        $session = $this->requestStack->getCurrentRequest()->getSession();

        $emptyCells = [];

        foreach ($this->board as $rowKeys => $row) {
            foreach ($row as $colKeys => $col) {
                if ($col === null) {
                    $emptyCells[][] = [
                        'row' => $rowKeys,
                        'col' => $colKeys,
                    ];
                }
            }
        }

        if (!empty($emptyCells)) {

            $randIndex = array_rand($emptyCells);
            $randRow = $emptyCells[$randIndex];

            if ($request->get('row') !== 0) {
                if ($request->get('col') !== null) {
                    $this->board[$randRow['row']][$randRow['col']] = $this->player;
                }
                $this->player = $this->player === "X" ? "O" : "X";
            }
            $this->checkGameResult();

            $session->set(self::SINGLEPLAYER_SESSION, [
                'board' => $this->board,
                'player' => $this->player,
            ]);
        }
    }

    public function getBoard(): array
    {
        return $this->board;
    }

    public function removeSession(): void
    {
        $session = $this->requestStack->getCurrentRequest()->getSession();

        if ($session->isStarted() && $session->has(self::SINGLEPLAYER_SESSION)) {
            $session->remove(self::SINGLEPLAYER_SESSION);
        }

    }

}