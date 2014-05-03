<?php

namespace Venyii\WhosePoints\Model;

class Season
{
    private $id;
    private $episodes;

    public function __construct($id)
    {
        $this->id = $id;
        $this->episodes = array();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    public function getPrettyId()
    {
        return $this->id < 10 ? '0'.$this->id : $this->id;
    }

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

    public function getPoints()
    {
        $p = 0;
        foreach ($this->episodes as $e) {
            $p += $e->getPoints();
        }

        return $p;
    }
}
