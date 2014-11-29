<?php

namespace Venyii\WhosePoints\Model;

use Venyii\WhosePoints\PointsHolderInterface;

class Game implements PointsHolderInterface
{
    private $name;
    private $participantPoints;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
        $this->participantPoints = array();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param ParticipantPoints $participantPoints
     * @throws \InvalidArgumentException
     */
    public function appendParticipantPoints(ParticipantPoints $participantPoints)
    {
        if (isset($this->participantPoints[$participantPoints->getParticipant()->getName()])) {
            throw new \InvalidArgumentException('Participant has already been added.');
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

    /**
     * @return double
     */
    public function getPoints()
    {
        $p = 0;
        foreach ($this->participantPoints as $pp) {
            $p += $pp->getPoints();
        }

        return $p;
    }
}
