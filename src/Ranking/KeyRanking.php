<?php

namespace Venyii\WhosePoints\Ranking;

use Venyii\WhosePoints\PointsHolderInterface;

class KeyRanking
{
    protected $pointHolders;

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

        arsort($ranks);

        return $ranks;
    }

    /**
     * @return PointsHolderInterface[]
     */
    public function group()
    {
        return $this->pointHolders;
    }
}
