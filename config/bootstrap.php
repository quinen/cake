<?php

//use MtxRessources\Core\MtxConfig;

//require_once dirname(__DIR__) . DS . "src" . DS . "functions.php";

if (file_exists(__DIR__ . "/factory.php")) {
    require_once(__DIR__ . "/factory.php");
} else {
    die('plugin QuinenCake ::: config/factory.php absent, veuillez creer le fichier');
}

/*
$localFile = "local." . MtxConfig::getEnvironment() . ".php";
if (file_exists(__DIR__ . DS . $localFile)) {
    require_once(__DIR__ . DS . $localFile);
} else {
    die('plugin UI ::: config/local.' . MtxConfig::getEnvironment() . '.php absent, veuillez creer le fichier');
}
*/