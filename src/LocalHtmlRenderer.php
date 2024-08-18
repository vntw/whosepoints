<?php

namespace Venyii\WhosePoints;

use Twig\Environment;

class LocalHtmlRenderer
{
    private Environment $engine;

    public function __construct(Environment $engine)
    {
        $this->engine = $engine;
    }

    public function render(array $stats, array $seasons, array $kisses)
    {
        $tpls = [];

        $this->engine->addGlobal('staticVersion', md5(uniqid().time()));
        $this->engine->addGlobal('seasons', $seasons);

        foreach ($seasons as $season) {
            $tpls['seasons'][$season->getId()] = $this->engine->render('season.html.twig', [
                'stats' => $stats,
                'season' => $season
            ]);

            foreach ($season->getEpisodes() as $episode) {
                $tpls['episodes'][$episode->getId()] = $this->engine->render('episode.html.twig', [
                    'stats' => $stats,
                    'season' => $season,
                    'episode' => $episode
                ]);
            }
        }

        $tpls['kisses'] = $this->engine->render('kisses.html.twig', [
            'kisses' => $kisses
        ]);
        $tpls['alltime'] = $this->engine->render('alltime.html.twig', [
            'stats' => $stats,
            'seasons' => $seasons
        ]);
        $tpls['index'] = $this->engine->render('index.html.twig', [
            'stats' => $stats,
            'seasons' => $seasons
        ]);

        return $tpls;
    }
}
