<?php

namespace Venyii\WhosePoints;

use Venyii\WhosePoints\Model\Season;
use Venyii\WhosePoints\Ranking\KeyRanking;
use Venyii\WhosePoints\Ranking\ParticipantPointsRanking;

class StatsGenerator
{
    const MIN_LIMIT = 10000;

    /**
     * @var array
     */
    private $stats;

    /**
     * @var Season[]
     */
    private $seasons;

    /**
     * @param Season[] $seasons
     */
    public function __construct(array $seasons)
    {
        $this->seasons = $seasons;
    }

    /**
     * @return array
     */
    public function generateStats()
    {
        if ($this->stats !== null) {
            return $this->stats;
        }

        $this->stats = [];

        $this->calcAllEpisodesCount();
        $this->calcAllSeasonsPoints();
        $this->calcSingleSeasonPointsRanking();
        $this->calcSingleEpisodePointsRanking();
        $this->calcAllSeasonsPointsRanking();
        $this->calcSingleSeasonGameRanking();
        $this->calcAllSeasonsGameRanking();
        $this->calcSingleSeasonGameCounts();
        $this->calcAllSeasonsGameCounts();
        $this->calcSingleSeasonParticipantRanking();
        $this->calcAlltimeParticipantRanking();
        $this->calcSingleSeasonWinnerRanking();
        $this->calcAllSeasonsWinnerRanking();
        $this->calcAlltimePoints();
        $this->calcSingleSeasonAvgGamesPerEpisode();
        $this->calcAlltimeAvgGamesPerEpisode();
        $this->calcSingleSeasonAvgPointsPerEpisode();
        $this->calcAlltimeAvgPointsPerEpisode();
        $this->calcAllSeasonsPointsRankingJson();
        $this->calcSingleSeasonsPointsRankingJson();
        $this->calcSingleSeasonParticipantRankingJson();
        $this->calcAllSeasonsParticipantRankingJson();
        $this->calcSingleEpisodePointsRankingJson();
        $this->calcSingleSeasonGameRankingJson();
        $this->calcAllSeasonsGameRankingJson();

        return $this->stats;
    }

    private function calcAllEpisodesCount()
    {
        $eps = 0;

        foreach ($this->seasons as $season) {
            $eps += count($season->getEpisodes());
        }

        $this->stats['allEpisodesCount'] = $eps;
    }

    private function calcAllSeasonsPoints()
    {
        foreach ($this->seasons as $season) {
            $this->stats['allSeasonPoints'][$season->getId()] = $season->getPoints();
        }
    }

    private function calcSingleSeasonPointsRanking()
    {
        foreach ($this->seasons as $season) {
            $pp = [];
            foreach ($season->getEpisodes() as $episode) {
                foreach ($episode->getGames() as $game) {
                    $pp = array_merge($pp, array_values($game->getParticipantPoints()));
                }
            }

            $r = new ParticipantPointsRanking($pp);
            $this->stats['singleSeasonPointsRanking'][$season->getId()] = $r->rank();
        }
    }

    private function calcSingleEpisodePointsRanking()
    {
        foreach ($this->seasons as $season) {
            foreach ($season->getEpisodes() as $episode) {
                $pp = [];
                foreach ($episode->getGames() as $game) {
                    $pp = array_merge($pp, array_values($game->getParticipantPoints()));
                }
                $r = new ParticipantPointsRanking($pp);
                $this->stats['singleEpisodePointsRanking'][$episode->getId()] = $r->rank();
            }
        }
    }

    private function calcAllSeasonsPointsRanking()
    {
        $pp = [];

        foreach ($this->stats['singleSeasonPointsRanking'] as $k => $sr) {
            $pp = array_merge($pp, array_values($sr));
        }

        $r = new ParticipantPointsRanking($pp);
        $this->stats['allSeasonsPointsRanking'] = $r->rank();
    }

    private function calcSingleSeasonGameRanking()
    {
        foreach ($this->seasons as $season) {
            $games = [];
            foreach ($season->getEpisodes() as $episode) {
                foreach ($episode->getGames() as $game) {
                    if (!isset($games[$game->getName()])) {
                        $games[$game->getName()] = 1;
                        continue;
                    }

                    $games[$game->getName()]++;
                }
            }

            $r = new KeyRanking($games);
            $this->stats['singleSeasonGameRanking'][$season->getId()] = $r->rank();
        }
    }

    private function calcAllSeasonsGameRanking()
    {
        $games = [];

        foreach ($this->stats['singleSeasonGameRanking'] as $sgames) {
            foreach ($sgames as $game => $plays) {
                if (!isset($games[$game])) {
                    $games[$game] = $plays;
                    continue;
                }

                $games[$game] += $plays;
            }
        }

        $r = new KeyRanking($games);
        $this->stats['allSeasonsGameRanking'] = $r->rank();
    }

    private function calcSingleSeasonGameCounts()
    {
        foreach ($this->stats['singleSeasonGameRanking'] as $sid => $sgames) {
            $this->stats['singleSeasonGameCounts'][$sid] = [
                'grouped' => count($sgames),
                'total' => array_sum($sgames)
            ];
        }
    }

    private function calcAllSeasonsGameCounts()
    {
        $this->stats['allSeasonsGameCounts'] = [
            'grouped' => count($this->stats['allSeasonsGameRanking']),
            'total' => array_sum($this->stats['allSeasonsGameRanking'])
        ];
    }

    private function calcSingleSeasonParticipantRanking()
    {
        foreach ($this->seasons as $season) {
            $ptcpts = [];

            foreach ($season->getEpisodes() as $episode) {
                foreach ($episode->getGames() as $game) {
                    foreach ($game->getParticipantPoints() as $ptcpt) {
                        if (!$ptcpt->hasParticipated()) {
                            continue;
                        }

                        if (!isset($ptcpts[$ptcpt->getParticipant()->getName()])) {
                            $ptcpts[$ptcpt->getParticipant()->getName()] = 1;
                            continue;
                        }

                        $ptcpts[$ptcpt->getParticipant()->getName()]++;
                    }
                }
            }

            $r = new KeyRanking($ptcpts);
            $this->stats['singleSeasonParticipantRanking'][$season->getId()] = $r->rank();
        }
    }

    private function calcAlltimeParticipantRanking()
    {
        $ptcpts = [];
        foreach ($this->stats['singleSeasonParticipantRanking'] as $sid => $ptcptss) {
            foreach ($ptcptss as $name => $ptcpd) {
                if (!isset($ptcpts[$name])) {
                    $ptcpts[$name] = $ptcpd;
                    continue;
                }

                $ptcpts[$name] += $ptcpd;
            }
        }

        $r = new KeyRanking($ptcpts);
        $this->stats['alltimeParticipantRanking'] = $r->rank();
    }

    private function calcSingleSeasonWinnerRanking()
    {
        foreach ($this->seasons as $season) {
            $winners = [];
            foreach ($season->getEpisodes() as $episode) {
                foreach ($episode->getWinners() as $participant) {
                    if (!isset($winners[$participant->getName()])) {
                        $winners[$participant->getName()] = 0;
                    }

                    $winners[$participant->getName()]++;
                }
            }

            $r = new KeyRanking($winners);
            $this->stats['singleSeasonWinnerRanking'][$season->getId()] = $r->rank();
        }
    }

    private function calcAllSeasonsWinnerRanking()
    {
        $winners = [];
        foreach ($this->stats['singleSeasonWinnerRanking'] as $sid => $wnnrs) {
            foreach ($wnnrs as $name => $wins) {
                if (!isset($winners[$name])) {
                    $winners[$name] = $wins;
                    continue;
                }

                $winners[$name] += $wins;
            }
        }

        $r = new KeyRanking($winners);
        $this->stats['allSeasonsWinnerRanking'] = $r->rank();
    }

    private function calcAlltimePoints()
    {
        $p = 0;

        foreach ($this->stats['allSeasonPoints'] as $sp) {
            $p += $sp;
        }

        $this->stats['alltimePoints'] = $p;
    }

    private function calcSingleSeasonAvgGamesPerEpisode()
    {
        foreach ($this->seasons as $season) {
            $this->stats['singleSeasonAvgGamesPerEpisode'][$season->getId()] = $this->stats['singleSeasonGameCounts'][$season->getId()]['total'] / count($season->getEpisodes());
        }
    }

    private function calcAlltimeAvgGamesPerEpisode()
    {
        $episodes = 0;
        foreach ($this->seasons as $season) {
            $episodes += count($season->getEpisodes());
        }

        $this->stats['alltimeAvgGamesPerEpisode'] = array_sum($this->stats['allSeasonsGameRanking']) / $this->stats['allEpisodesCount'];
    }

    private function calcSingleSeasonAvgPointsPerEpisode()
    {
        foreach ($this->seasons as $season) {
            $this->stats['singleSeasonAvgPointsPerEpisode'][$season->getId()] = $season->getPoints() / count($season->getEpisodes());
        }
    }

    private function calcAlltimeAvgPointsPerEpisode()
    {
        $this->stats['alltimeAvgPointsPerEpisode'] = $this->stats['alltimePoints'] / $this->stats['allEpisodesCount'];
    }

    private function calcAllSeasonsPointsRankingJson()
    {
        $json = [];
        $others = ['Others', 0];
        foreach ($this->stats['allSeasonsPointsRanking'] as $ptcp) {
            if ($ptcp->getPoints() <= self::MIN_LIMIT) {
                $others[1] += $ptcp->getPoints();
            } else {
                $json[] = [
                    $ptcp->getParticipant()->getName(),
                    $ptcp->getPoints()
                ];
            }
        }
        $json[] = $others;
        $this->stats['allSeasonsPointsRankingJson'] = json_encode($json);
    }

    private function calcSingleSeasonsPointsRankingJson()
    {
        foreach ($this->stats['singleSeasonPointsRanking'] as $sid => $ptcps) {
            $json = [];
            $others = ['Others', 0];
            foreach ($ptcps as $ptcp) {
                if ($ptcp->getPoints() <= self::MIN_LIMIT) {
                    $others[1] += $ptcp->getPoints();
                } else {
                    $json[] = [
                        $ptcp->getParticipant()->getName(),
                        $ptcp->getPoints()
                    ];
                }
            }
            $json[] = $others;
            $this->stats['singleSeasonPointsRankingJson'][$sid] = json_encode($json);
        }
    }

    private function calcSingleSeasonParticipantRankingJson()
    {
        foreach ($this->stats['singleSeasonParticipantRanking'] as $sid => $ptcps) {
            $json = [];
            foreach ($ptcps as $name => $ptcpd) {
                $json[] = [$name, $ptcpd];
            }
            $this->stats['singleSeasonParticipantRankingJson'][$sid] = json_encode($json);
        }
    }

    private function calcAllSeasonsParticipantRankingJson()
    {
        $json = [];
        foreach ($this->stats['alltimeParticipantRanking'] as $name => $ptcpd) {
            $json[] = [$name, $ptcpd];
        }
        $this->stats['alltimeParticipantRankingJson'] = json_encode($json);
    }

    private function calcSingleEpisodePointsRankingJson()
    {
        foreach ($this->stats['singleEpisodePointsRanking'] as $episodeId => $participants) {
            $json = [];
            foreach ($participants as $participant) {
                if ($participant->getPoints() <= 0) {
                    continue;
                }
                $json[] = [$participant->getParticipant()->getName(), $participant->getPoints()];
            }
            $this->stats['singleEpisodePointsRankingJson'][$episodeId] = json_encode($json);
        }
    }

    private function calcSingleSeasonGameRankingJson()
    {
        foreach ($this->stats['singleSeasonGameRanking'] as $sid => $games) {
            $json = [];
            foreach ($games as $game => $played) {
                $json[] = [$game, $played];
            }
            $this->stats['singleSeasonGameRankingJson'][$sid] = json_encode($json);
        }
    }

    private function calcAllSeasonsGameRankingJson()
    {
        $json = [];
        foreach ($this->stats['allSeasonsGameRanking'] as $name => $played) {
            $json[] = [$name, $played];
        }
        $this->stats['allSeasonsGameRankingJson'] = json_encode($json);
    }
}
