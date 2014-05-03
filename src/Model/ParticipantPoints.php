<?php

namespace Venyii\WhosePoints\Model;

use Venyii\WhosePoints\PointsHolderInterface;

class ParticipantPoints implements PointsHolderInterface
{
    private $participant;
    private $points;
    /**
     * Has received points, but did not participate in the game
     *
     * @var bool
     */
    private $participated;

    public function __construct(Participant $participant, $points, $participated = true)
    {
        $this->participant = $participant;
        $this->points = $points;
        $this->participated = $participated;
    }

    /**
     * @return Participant
     */
    public function getParticipant()
    {
        return $this->participant;
    }

    /**
     * @param double|null $points
     */
    public function setPoints($points)
    {
        $this->points = $points;
    }

    /**
     * @return double|null
     */
    public function getPoints()
    {
        return $this->points;
    }

    public function hasParticipated()
    {
        return $this->participated;
    }
}
