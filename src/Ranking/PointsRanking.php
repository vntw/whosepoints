<?php

namespace Venyii\WhosePoints\Ranking;

use Venyii\WhosePoints\PointsHolderInterface;

abstract class PointsRanking
{
    /**
     * @var PointsHolderInterface[]
     */
    protected $pointHolders;

    /**
     * @param PointsHolderInterface[] $pointHolders
     */
    public function __construct(array $pointHolders)
    {
        $this->pointHolders = $pointHolders;
    }

    /**
     * @return array
     */
    public function rank()
    {
        $ranks = $this->group();

        usort(
            $ranks,
            function (PointsHolderInterface $a, PointsHolderInterface $b) {
                if ($a->getPoints() > $b->getPoints()) {
                    return -1;
                } elseif ($a->getPoints() < $b->getPoints()) {
                    return 1;
                }

                return 0;
            }
        );

        return $ranks;
    }

    /**
     * @return PointsHolderInterface[]
     */
    abstract public function group();
}
