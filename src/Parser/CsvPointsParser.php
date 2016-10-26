<?php

namespace Venyii\WhosePoints\Parser;

use Venyii\WhosePoints\Model\Season;
use Venyii\WhosePoints\Model\Episode;
use Venyii\WhosePoints\Model\Game;
use Venyii\WhosePoints\Model\Participant;
use Venyii\WhosePoints\Model\ParticipantPoints;

class CsvPointsParser
{
    private $csv;
    private $currentData;
    /**
     * @var Episode
     */
    private $episode;
    /**
     * @var Season
     */
    private $season;
    /**
     * @var Season[]
     */
    private $seasons = [];
    /**
     * @var Participant[]
     */
    private $participants = [];
    private $participantsStartIndex = 4;
    private $excludePointValues = ['', '-'];
    private $pointsFormatter;

    public function __construct(\SplFileInfo $csv)
    {
        $this->csv = $csv;
        $this->pointsFormatter = \NumberFormatter::create('en_US', \NumberFormatter::DECIMAL);
    }

    /**
     * @return Participant[]
     */
    public function getParticipants()
    {
        return $this->participants;
    }

    public function parse()
    {
        $i = -1;
        $handle = fopen($this->csv->getRealPath(), 'r');

        while (false !== ($this->currentData = fgetcsv($handle))) {
            $i++;

            if ($i === 0) {
                $this->readParticipants();
                continue;
            }

            if ('' !== $this->currentData[0]) {
                // new episode/season
                $this->readNewEpisode();
            }

            if ('' === $this->currentData[2]) {
                // no more games
                break;
            }

            $this->readGameParticipants();

            if ('' !== $this->currentData[1]) {
                $this->readWinners();
            }
        }

        fclose($handle);
        unset($this->currentData);

        return $this->seasons;
    }

    private function readParticipants()
    {
        $ptcps = array_slice(
            $this->currentData,
            $this->participantsStartIndex,
            count($this->currentData) - $this->participantsStartIndex,
            true
        );

        foreach ($ptcps as $id => $ptcp) {
            if (0 !== preg_match('/(.*)\s\(([A-Z]{2,3})\)$/', $ptcp, $ptcpCreds)) {
                $name = $ptcpCreds[1];
                $alias = $ptcpCreds[2];
            } else {
                $name = $ptcp;
                $alias = null;
            }

            $this->participants[$id] = new Participant($id, $name, $alias);
        }
    }

    private function readWinners()
    {
        $winners = [];
        $winnerAliases = explode(',', $this->currentData[1]);

        foreach ($winnerAliases as $winner) {
            foreach ($this->participants as $participant) {
                if ($participant->getAlias() === $winner) {
                    if (isset($winners[$participant->getId()])) {
                        throw new \Exception('dupl winner');
                    }

                    $winners[] = $participant;
                }
            }
        }

        if (empty($winners)) {
            throw new \Exception('Unresolved winners: '.$this->currentData[1]);
        }

        $this->episode->setWinners($winners);
    }

    private function readGameParticipants()
    {
        $game = new Game($this->currentData[2]);
        $realParticipants = '' !== $this->currentData[3] ? explode(',', $this->currentData[3]) : [];
        $hasRealParticipants = !empty($realParticipants);

        foreach ($this->participants as $participant) {
            if (in_array($this->currentData[$participant->getId()], $this->excludePointValues)) {
                continue;
            }

            $participated = true;

            if ($hasRealParticipants && !in_array($participant->getAlias(), $realParticipants, true)) {
                $participated = false;
            }

            $points = $this->pointsFormatter->parse($this->currentData[$participant->getId()]);
            $game->appendParticipantPoints(new ParticipantPoints($participant, $points, $participated));
        }

        $this->episode->appendGame($game);
    }

    private function readNewEpisode()
    {
        preg_match('/^S(\d+)E\d+/', $this->currentData[0], $seasonData);

        if (isset($seasonData[1])) {
            $seasonId = (int) $seasonData[1];

            if (!$this->season || ($this->season && $this->season->getId() !== $seasonId)) {
                $this->season = new Season($seasonId);
                $this->seasons[$seasonId] = $this->season;
            }
        }

        $this->episode = new Episode($this->currentData[0]);
        $this->season->appendEpisode($this->episode);
    }
}
