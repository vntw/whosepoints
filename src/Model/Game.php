<?php

namespace Venyii\WhosePoints\Model;

use Venyii\WhosePoints\PointsHolderInterface;

class Game implements PointsHolderInterface
{
    private $name;
    private $participantPoints;

    public function __construct($name)
    {
        $this->name = $name;
        $this->participantPoints = array();
    }

    public function getName()
    {
        return $this->name;
    }

    public function appendParticipantPoints(ParticipantPoints $participantPoints)
    {
        if (isset($this->participantPoints[$participantPoints->getParticipant()->getName()])) {
            throw new \InvalidArgumentException('dbl ptcp');
        }

        $this->participantPoints[$participantPoints->getParticipant()->getName()] = $participantPoints;
    }

    /**
     * @return ParticipantPoints[]
     */
    public function getParticipantPoints()
    {
        return $this->participantPoints;
    }

    public function getPoints()
    {
        $p = 0;
        foreach ($this->participantPoints as $pp) {
            $p += $pp->getPoints();
        }

        return $p;
    }
}
