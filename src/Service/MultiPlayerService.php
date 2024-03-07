<?php

namespace App\Service;

use JetBrains\PhpStorm\NoReturn;
use Symfony\Component\HttpFoundation\RequestStack;

class MultiPlayerService extends GameService
{
    const MULTIPLAYER_SESSION = 'multi';
    public function __construct(private readonly RequestStack $requestStack)
    {
        if (
            $this->requestStack->getCurrentRequest()
            && $this->requestStack->getCurrentRequest()->getSession()
            && $this->requestStack->getCurrentRequest()->getSession()->has(self::MULTIPLAYER_SESSION)
            && $this->requestStack->getCurrentRequest()->getSession()->get(self::MULTIPLAYER_SESSION, $this->getBoard())

        ) {
            $data = $this->requestStack->getCurrentRequest()->getSession()->get(self::MULTIPLAYER_SESSION);
            $this->player = $data['player'];
            $this->board = $data['board'];
        } else {
            $this->board = array_fill(0, 3, array_fill(0, 3, null));
            $this->player = "X";
        }
    }

    #[NoReturn] public function setPlayersMoves($row, $col): void
    {
        $session = $this->requestStack->getCurrentRequest()->getSession();

        if ($this->board[$row][$col] == null) {
            $this->board[$row][$col]= $this->player;
            $this->player = $this->player === "X" ? "O" : "X";
        }

        $this->checkGameResult();

        $session->set(self::MULTIPLAYER_SESSION, [
            'board' => $this->board,
            'player' => $this->player
        ]);
    }

    public function getBoard(): array
    {
        return $this->board;
    }

    public function reset(): void
    {
        unset($_SESSION['board']);
    }

    public function removeSession(): void
    {
        $session = $this->requestStack->getCurrentRequest()->getSession();

        if ($session->isStarted() && $session->has(self::MULTIPLAYER_SESSION)) {
            $session->remove(self::MULTIPLAYER_SESSION);
        }

    }



}