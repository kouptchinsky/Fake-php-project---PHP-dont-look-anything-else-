<?php
date_default_timezone_set('Europe/Brussels');
$hote = 'localhost';
$nomBD = 'heh_impression';
$user = 'root';
$mdpDB = '';
try {
    $DB = new PDO('mysql:host='.$hote.';dbname='.$nomBD, $user, $mdpDB);
    $DB->exec("SET NAMES 'utf8'");
} catch (Exception $e) {
    echo "Erreur de connexion à la BD : $e";
}
