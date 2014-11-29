<?php

namespace Venyii\WhosePoints\Model;

class Episode
{
    private $id;
    private $games;
    private $winners;

    /**
     * @param int $id
     */
    public function __construct($id)
    {
        $this->id = $id;
        $this->games = array();
        $this->winners = array();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param Game $game
     * @throws \InvalidArgumentException
     */
    public function appendGame(Game $game)
    {
        if (isset($this->games[$game->getName()])) {
            throw new \InvalidArgumentException('The game has already been added.');
        }

        $this->games[$game->getName()] = $game;
    }

    /**
     * @return Game[]
     */
    public function getGames()
    {
        return $this->games;
    }

    /**
     * @param array $winners
     */
    public function setWinners(array $winners)
    {
        $this->winners = $winners;
    }

    /**
     * @return array
     */
    public function getWinners()
    {
        return $this->winners;
    }

    /**
     * @return double
     */
    public function getPoints()
    {
        $p = 0;
        foreach ($this->games as $g) {
            $p += $g->getPoints();
        }

        return $p;
    }
}
