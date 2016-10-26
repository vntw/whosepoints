<?php

namespace Venyii\WhosePoints\Ranking;

use Venyii\WhosePoints\Model\Participant;
use Venyii\WhosePoints\Model\ParticipantPoints;

class ParticipantPointsRanking extends PointsRanking
{
    public function group()
    {
        $pointHolders = [];

        foreach ($this->pointHolders as $participantPoint) {
            /* @var $participantPoint ParticipantPoints */

            $participant = $participantPoint->getParticipant();
            /* @var $participant Participant */

            if (isset($pointHolders[$participant->getId()])) {
                $participantPoints = $pointHolders[$participant->getId()];
                /* @var $participantPoints ParticipantPoints */
                $participantPoints->setPoints($participantPoints->getPoints() + $participantPoint->getPoints());

                continue;
            }

            $pointHolders[$participant->getId()] = clone $participantPoint;
        }

        return array_values($pointHolders);
    }
}
