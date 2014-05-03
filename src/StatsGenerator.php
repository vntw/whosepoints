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

    public function __construct(array $seasons)
    {
        $this->stats = array();
        $this->seasons = $seasons;
    }

    public function getStats()
    {
        return $this->stats;
    }

    /**
     * @return $this
     */
    public function generate()
    {
        $refl = new \ReflectionClass($this);

        foreach ($refl->getMethods(\ReflectionMethod::IS_PRIVATE) as $method) {
            if (0 === strpos($method->getName(), 'sgen')) {
                $method->setAccessible(true);
                $method->invoke($this);
            }
        }

        return $this;
    }

    private function sgenAllEpisodesCount()
    {
        $eps = 0;

        foreach ($this->seasons as $season) {
            $eps += count($season->getEpisodes());
        }

        $this->stats['allEpisodesCount'] = $eps;
    }

    private function sgenAllSeasonsPoints()
    {
        foreach ($this->seasons as $season) {
            $this->stats['allSeasonPoints'][$season->getId()] = $season->getPoints();
        }
    }

    private function sgenSingleSeasonPointsRanking()
    {
        foreach ($this->seasons as $season) {
            $pp = array();
            foreach ($season->getEpisodes() as $episode) {
                foreach ($episode->getGames() as $game) {
                    $pp = array_merge($pp, array_values($game->getParticipantPoints()));
                }
            }

            $r = new ParticipantPointsRanking($pp);
            $this->stats['singleSeasonPointsRanking'][$season->getId()] = $r->rank();
        }
    }

    private function sgenSingleEpisodePointsRanking()
    {
        foreach ($this->seasons as $season) {
            foreach ($season->getEpisodes() as $episode) {
                $pp = array();
                foreach ($episode->getGames() as $game) {
                    $pp = array_merge($pp, array_values($game->getParticipantPoints()));
                }
                $r = new ParticipantPointsRanking($pp);
                $this->stats['singleEpisodePointsRanking'][$episode->getId()] = $r->rank();
            }
        }
    }

    private function sgenAllSeasonsPointsRanking()
    {
        $pp = array();

        foreach ($this->stats['singleSeasonPointsRanking'] as $k => $sr) {
            $pp = array_merge($pp, array_values($sr));
        }

        $r = new ParticipantPointsRanking($pp);
        $this->stats['allSeasonsPointsRanking'] = $r->rank();
    }

    private function sgenSingleSeasonGameRanking()
    {
        foreach ($this->seasons as $season) {
            $games = array();
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

    private function sgenAllSeasonsGameRanking()
    {
        $games = array();

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

    private function sgenSingleSeasonGameCounts()
    {
        foreach ($this->stats['singleSeasonGameRanking'] as $sid => $sgames) {
            $this->stats['singleSeasonGameCounts'][$sid] = array(
                'grouped' => count($sgames),
                'total' => array_sum($sgames)
            );
        }
    }

    private function sgenAllSeasonsGameCounts()
    {
        $this->stats['allSeasonsGameCounts'] = array(
            'grouped' => count($this->stats['allSeasonsGameRanking']),
            'total' => array_sum($this->stats['allSeasonsGameRanking'])
        );
    }

    private function sgenSingleSeasonParticipantRanking()
    {
        foreach ($this->seasons as $season) {
            $ptcpts = array();

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

    private function sgenAlltimeParticipantRanking()
    {
        $ptcpts = array();
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

    private function sgenSingleSeasonWinnerRanking()
    {
        foreach ($this->seasons as $season) {
            $winners = array();
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

    private function sgenAllSeasonsWinnerRanking()
    {
        $winners = array();
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

    private function sgenAlltimePoints()
    {
        $p = 0;

        foreach ($this->stats['allSeasonPoints'] as $sp) {
            $p += $sp;
        }

        $this->stats['alltimePoints'] = $p;
    }

    private function sgenSingleSeasonAvgGamesPerEpisode()
    {
        foreach ($this->seasons as $season) {
            $this->stats['singleSeasonAvgGamesPerEpisode'][$season->getId()] = $this->stats['singleSeasonGameCounts'][$season->getId()]['total'] / count($season->getEpisodes());
        }
    }

    private function sgenAlltimeAvgGamesPerEpisode()
    {
        $episodes = 0;
        foreach ($this->seasons as $season) {
            $episodes += count($season->getEpisodes());
        }

        $this->stats['alltimeAvgGamesPerEpisode'] = array_sum($this->stats['allSeasonsGameRanking']) / $this->stats['allEpisodesCount'];
    }

    private function sgenSingleSeasonAvgPointsPerEpisode()
    {
        foreach ($this->seasons as $season) {
            $this->stats['singleSeasonAvgPointsPerEpisode'][$season->getId()] = $season->getPoints() / count($season->getEpisodes());
        }
    }

    private function sgenAlltimeAvgPointsPerEpisode()
    {
        $this->stats['alltimeAvgPointsPerEpisode'] = $this->stats['alltimePoints'] / $this->stats['allEpisodesCount'];
    }

    private function sgenAllSeasonsPointsRankingJson()
    {
        $json = array();
        $others = array('Others', 0);
        foreach ($this->stats['allSeasonsPointsRanking'] as $ptcp) {
            if (!$ptcp->hasParticipated()) {
//                continue;
            }

            if ($ptcp->getPoints() <= self::MIN_LIMIT) {
                $others[1] += $ptcp->getPoints();
            } else {
                $json[] = array(
                    $ptcp->getParticipant()->getName(),
                    $ptcp->getPoints()
                );
            }
        }
        $json[] = $others;
        $this->stats['allSeasonsPointsRankingJson'] = json_encode($json);
    }

    private function sgenSingleSeasonsPointsRankingJson()
    {
        foreach ($this->stats['singleSeasonPointsRanking'] as $sid => $ptcps) {
            $json = array();
            $others = array('Others', 0);
            foreach ($ptcps as $ptcp) {
                if (!$ptcp->hasParticipated()) {
//                continue;
                }

                if ($ptcp->getPoints() <= self::MIN_LIMIT) {
                    $others[1] += $ptcp->getPoints();
                } else {
                    $json[] = array(
                        $ptcp->getParticipant()->getName(),
                        $ptcp->getPoints()
                    );
                }
            }
            $json[] = $others;
            $this->stats['singleSeasonPointsRankingJson'][$sid] = json_encode($json);
        }
    }

    private function sgenSingleSeasonParticipantRankingJson()
    {
        foreach ($this->stats['singleSeasonParticipantRanking'] as $sid => $ptcps) {
            $json = array();
            foreach ($ptcps as $name => $ptcpd) {
                $json[] = array($name, $ptcpd);
            }
            $this->stats['singleSeasonParticipantRankingJson'][$sid] = json_encode($json);
        }
    }

    private function sgenAllSeasonsParticipantRankingJson()
    {
        $json = array();
        foreach ($this->stats['alltimeParticipantRanking'] as $name => $ptcpd) {
            $json[] = array($name, $ptcpd);
        }
        $this->stats['alltimeParticipantRankingJson'] = json_encode($json);
    }

    private function sgenSingleEpisodePointsRankingJson()
    {
        foreach ($this->stats['singleEpisodePointsRanking'] as $episodeId => $participants) {
            $json = array();
            foreach ($participants as $participant) {
                if ($participant->getPoints() <= 0) {
                    continue;
                }
                $json[] = array($participant->getParticipant()->getName(), $participant->getPoints());
            }
            $this->stats['singleEpisodePointsRankingJson'][$episodeId] = json_encode($json);
        }
    }

    private function sgenSingleSeasonGameRankingJson()
    {
        foreach ($this->stats['singleSeasonGameRanking'] as $sid => $games) {
            $json = array();
            foreach ($games as $game => $played) {
                $json[] = array($game, $played);
            }
            $this->stats['singleSeasonGameRankingJson'][$sid] = json_encode($json);
        }
    }

    private function sgenAllSeasonsGameRankingJson()
    {
        $json = array();
        foreach ($this->stats['allSeasonsGameRanking'] as $name => $played) {
            $json[] = array($name, $played);
        }
        $this->stats['allSeasonsGameRankingJson'] = json_encode($json);
    }

    private function sgenParticipantGameMatrix()
    {

    }
}
