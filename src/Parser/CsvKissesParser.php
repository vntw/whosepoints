<?php

namespace Venyii\WhosePoints\Parser;

use Venyii\WhosePoints\Model\Season;
use Venyii\WhosePoints\Model\Episode;
use Venyii\WhosePoints\Model\Participant;

class CsvKissesParser
{
    private $csv;
    private $seasons;
    private $participants;
    private $kisses;
    private $currentData;

    public function __construct(\SplFileInfo $csv, array $seasons, array $participants)
    {
        $this->csv = $csv;
        $this->kisses = array();
        $this->seasons = $seasons;

        if (empty($participants)) {
            throw new \InvalidArgumentException('No participants!');
        }

        $this->participants = $participants;
    }

    public function parse()
    {
        $i = -1;
        $episode = null;
        $currentEpisode = null;
        $handle = fopen($this->csv->getRealPath(), 'r');

        while (false !== ($this->currentData = fgetcsv($handle))) {
            $i++;

            if ($i === 0) {
                continue;
            }

            if (!$this->currentData[0] && !$currentEpisode) {
                throw new \Exception();
            }

            if ($this->currentData[0]) {
                $currentEpisode = $this->currentData[0];
                $episode = $this->findEpisode($currentEpisode);

                $this->kisses[$currentEpisode] = array(
                    'episode' => $episode,
                    'participants' => array()
                );
            }

            if (!$episode) {
                throw new \Exception();
            }

            $kissPtcpts = array();
            $ptcpts = explode('+', $this->currentData[1]);

            foreach ($ptcpts as $ptcpt) {
                foreach ($this->participants as $participant) {
                    if ($participant->getAlias() === $ptcpt) {
                        $kissPtcpts[] = $participant;
                    }
                }
            }

            if (count($kissPtcpts) !== 2) {
                throw new \Exception();
            }

            $this->kisses[$currentEpisode]['participants'][] = $kissPtcpts;
        }

        fclose($handle);
        unset($this->currentData);

        return $this->kisses;
    }

    private function findEpisode($id)
    {
        preg_match('/^S(\d+)E\d+/', $this->currentData[0], $seasonData);
        $seasonId = (int) $seasonData[1];

        foreach ($this->seasons as $season) {
            if ($season->getId() === $seasonId) {
                foreach ($season->getEpisodes() as $episode) {
                    if ($episode->getId() === $id) {
                        return $episode;
                    }
                }
            }
        }

        return null;
    }
}
