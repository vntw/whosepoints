<?php

namespace Venyii\WhosePoints\Model;

class Season
{
    private $id;
    private $episodes;

    /**
     * @param int $id
     */
    public function __construct($id)
    {
        $this->id = $id;
        $this->episodes = [];
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getPrettyId()
    {
        return str_pad($this->id, 2, '0', STR_PAD_LEFT);
    }

    /**
     * @param Episode $episode
     * @throws \InvalidArgumentException
     */
    public function appendEpisode(Episode $episode)
    {
        if (isset($this->episodes[$episode->getId()])) {
            throw new \InvalidArgumentException('dbl epi');
        }

        $this->episodes[$episode->getId()] = $episode;
    }

    /**
     * @return Episode[]
     */
    public function getEpisodes()
    {
        return $this->episodes;
    }

    /**
     * @return double
     */
    public function getPoints()
    {
        $p = 0;
        foreach ($this->episodes as $episode) {
            $p += $episode->getPoints();
        }

        return $p;
    }
}
