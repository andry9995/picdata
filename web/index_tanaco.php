<?php

try{

    $serveur = 'ns384250.ovh.net';
    $nom_base = 'dbboost';
    $login = 'dbboost';
    $password = 'lxV551#m';

    $conn = new PDO('mysql:host='.$serveur.';dbname='.$nom_base.';charset=UTF8', $login, $password,
        array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"));

    $req = 'select * from ecriture';
    $req_s = $conn->prepare($req);
    $req_s->execute();

    var_dump($req_s->fetchAll());

}
catch(Exception $ex)
{
    echo $ex->getMessage();
}

echo 'Tanaco.fr';