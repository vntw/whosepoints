<?php

namespace Venyii\WhosePoints\Model;

// TODO episode participants
class Episode
{
    private $id;
    private $games;
    private $winners;

    public function __construct($id)
    {
        $this->id = $id;
        $this->games = array();
        $this->winners = array();
    }

    public function getId()
    {
        return $this->id;
    }

    public function appendGame(Game $game)
    {
        if (isset($this->games[$game->getName()])) {
            throw new \InvalidArgumentException('dbl game');
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

    public function getPoints()
    {
        $p = 0;
        foreach ($this->games as $g) {
            $p += $g->getPoints();
        }

        return $p;
    }
}
