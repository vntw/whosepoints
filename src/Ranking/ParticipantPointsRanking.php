<?php

namespace Venyii\WhosePoints\Ranking;

use Venyii\WhosePoints\Model\ParticipantPoints;

class ParticipantPointsRanking extends PointsRanking
{
    public function group()
    {
        $pp = array();

        foreach ($this->pointHolders as $participantPoint) {
            /* @var $participantPoint ParticipantPoints */
            if (isset($pp[$participantPoint->getParticipant()->getId()])) {
                $papo = $pp[$participantPoint->getParticipant()->getId()];

                $oldPoints = $papo->getPoints();
                $newPoints = $participantPoint->getPoints();

                $papo->setPoints($oldPoints + $newPoints);
                continue;
            }

            $pp[$participantPoint->getParticipant()->getId()] = clone $participantPoint;
        }

        return array_values($pp);
    }
}
