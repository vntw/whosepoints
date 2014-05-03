Highcharts.getOptions().plotOptions.pie.colors = (function () {
    var colors = [],
        base = '#DF37FF',
        i;

    for (i = 0; i < 10; i++) {
        colors.push(Highcharts.Color(base).brighten((i - 3) / 7).get());
    }

    return colors;
}());

// TODO extend default chart object

if (typeof allSeasonsPointsRankingJson != 'undefined') {
    $('#chart-allSeasonsPointsRankingJson').highcharts({
        chart: {
            backgroundColor: '#ffffff'
        },
        title: {
            text: null
        },
        tooltip: {
            pointFormat: '{point.name}: <b>{point.y} Points</b>',
            headerFormat: ''
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                dataLabels: {
                    enabled: false
                },
                showInLegend: false
            },
            series: {
                point: {
                    events: {
                        click: function () {
                            return false;
                        }
                    }
                }
            }
        },
        series: [
            {
                type: 'pie',
                data: allSeasonsPointsRankingJson
            }
        ],
        credits: {
            enabled: false
        }
    });
}
if (typeof alltimeParticipantRankingJson != 'undefined') {
    $('#chart-alltimeParticipantRankingJson').highcharts({
        chart: {
            backgroundColor: '#ffffff'
        },
        title: {
            text: null
        },
        tooltip: {
            pointFormat: '{point.name}: <b>{point.y}x participated</b>',
            headerFormat: ''
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                dataLabels: {
                    enabled: false
                },
                showInLegend: false
            },
            series: {
                point: {
                    events: {
                        click: function () {
                            return false;
                        }
                    }
                }
            }
        },
        series: [
            {
                type: 'pie',
                data: alltimeParticipantRankingJson
            }
        ],
        credits: {
            enabled: false
        }
    });
}
if (typeof allSeasonsGameRankingJson != 'undefined') {
    $('#chart-allSeasonsGameRankingJson').highcharts({
        chart: {
            backgroundColor: '#ffffff'
        },
        title: {
            text: null
        },
        tooltip: {
            pointFormat: '{point.name}: <b>{point.y}x played</b>',
            headerFormat: ''
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                dataLabels: {
                    enabled: false
                },
                showInLegend: false
            },
            series: {
                point: {
                    events: {
                        click: function () {
                            return false;
                        }
                    }
                }
            }
        },
        series: [
            {
                type: 'pie',
                data: allSeasonsGameRankingJson
            }
        ],
        credits: {
            enabled: false
        }
    });
}

if (typeof singleEpisodePointsRankingJson != 'undefined') {
    $('#chart-singleEpisodePointsRankingJson').highcharts({
        chart: {
            backgroundColor: '#ffffff'
        },
        title: {
            text: null
        },
        tooltip: {
            pointFormat: '{point.name}: <b>{point.y} Points</b>',
            headerFormat: ''
        },
        legend: {
            align: 'left',
            verticalAlign: 'top',
            layout: 'vertical'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                dataLabels: {
                    enabled: false
                },
                showInLegend: true
            },
            series: {
                point: {
                    events: {
                        click: function () {
                            return false;
                        }
                    }
                }
            }
        },
        series: [
            {
                type: 'pie',
                data: singleEpisodePointsRankingJson
            }
        ],
        credits: {
            enabled: false
        }
    });
}