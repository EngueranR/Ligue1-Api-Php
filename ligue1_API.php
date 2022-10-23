<?php


include_once('simple_html_dom.php');


function getHtml()
{
    $html = file_get_html("https://www.maxifoot.fr/calendrier-ligue-1-france.htm");
    return $html;
}

function getDay()
{
    $html = getHtml();
    foreach ($html->find(".ch3") as $day) {
        $day = str_replace("^", " ", $day);
        $day = str_replace("top", " ", $day);
        $day = str_replace(",", " ", $day);
        $listeDay[] = $day;
    }
    return $listeDay;
}

function getDayTime()
{
    $html = getHtml();
    foreach ($html->find(".ch3") as $day) {
        $day = str_replace("^", "", $day);
        $day = str_replace("top", "", $day);
        $day = str_replace(",", "", $day);
        $day = str_replace("journée", "", $day);
        // retirer les jours
        $day = str_replace("lundi", "", $day);
        $day = str_replace("mardi", "", $day);
        $day = str_replace("mercredi", "", $day);
        $day = str_replace("jeudi", "", $day);
        $day = str_replace("vendredi", "", $day);
        $day = str_replace("samedi", "", $day);
        $day = str_replace("dimanche", "", $day);

        // retirer les "1e", "3e"
        for ($i = 0; $i < 39; $i++) {
            $day = str_replace($i . "e", " ", $day);
        }

        // retirer les dixaines des "10e" ,"30e"
        $day = str_replace(" 1 ", "", $day);
        $day = str_replace(" 2 ", "", $day);
        $day = str_replace(" 3 ", "", $day);
        $listeDay[] = $day;
    }
    return $listeDay;
}

function getHomeTeam()
{
    $html = getHtml();
    foreach ($html->find(".eqc text") as $team) {
        $getHomeTeam1[] = $team;
    }
    $i = 0;
    foreach ($getHomeTeam1 as $onlyHomeTeam) {
        if ($i % 2 == 0) {
            unset($getHomeTeam1[$i + 1]);
            $getHomeTeam2 = array_values($getHomeTeam1);
            $i++;
        } else $i++;
    }
    return $getHomeTeam2;
}

function getAwayTeam()
{
    $html = getHtml();
    foreach ($html->find(".eqc text") as $team) {
        $getAwayTeam1[] = $team;
    }
    $i = 1;
    foreach ($getAwayTeam1 as $onlyHomeTeam) {
        if ($i % 2 == 0) {
            unset($getAwayTeam1[$i - 2]);
            $getAwayTeam2 = array_values($getAwayTeam1);
            $i++;
        } else $i++;
    }
    return $getAwayTeam2;
}


function getScore() // Format "ScoreHome-ScoreAway"
{
    $html = getHtml();
    foreach ($html->find("th a[title] ") as $score) {
        $listScore[] = $score;
    }
    return $listScore;
}

function getScoreHomeTeam() // Format "ScoreHome-ScoreAway"
{
    $html = getHtml();
    foreach ($html->find("th a[title] text") as $score) {
        $listScore[] = substr($score, 0, 1);
    }
    return $listScore;
}

function getScoreAwayTeam() // Format "ScoreHome-ScoreAway"
{
    $html = getHtml();
    foreach ($html->find("th a[title] text") as $score) {
        $listScore[] = substr($score, 2, 1);
    }
    return $listScore;
}


function buildJson()
{
    $homeTeam = getHomeTeam();
    $awayTeam = getAwayTeam();
    $scoreHomeTeam = getScoreHomeTeam();
    $scoreAwayTeam = getScoreAwayTeam();
    // $dateDay = getDayTime();


    $json = "[";
    $j = 1;
    $c = 1;
    for ($i = 0; $i < 381; $i++) {
        $json .= "{";
        $json .= '"day":"' . ($j) . '",';
        //  $json .= '"dateMatch:"' . $dateDay[($j)] . '",';
        $json .= '"idMatch":"' .  ($j) . "_" . $c . '",';
        $json .= '"homeTeam":"' . $homeTeam[$i] . '",';
        $json .= '"awayTeam":"' . $awayTeam[$i] . '",';
        $json .= '"scoreHomeTeam":"' . $scoreHomeTeam[$i] . '",';
        $json .= '"scoreAwayTeam":"' . $scoreAwayTeam[$i] . '",';
        $json .= "},";
        $c++;
        // Permet d'avoir la date chaque 10 itérations on ajoute 1 à jour.
        if (($i + 1) % 10 == 0) {
            $j++;
        }
        // Dernier match = 39
        if ($j >= 38) {
            $j = 37;
        }

        // Permet d'avoir un idMatch de type Day_N°Match
        if (($i+1) % 10 == 0) {
            $c = 1;
        }
    }
    $json .= "]";git init


    $json = str_replace(",}", "}", $json);
    $json = str_replace(",]", "]", $json);
    return $json;
}


function generateJson(){
    file_put_contents('ligue1_API.json', buildJson());
}


generateJson();

