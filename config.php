<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "localhost";
$user = "root";  // Utilise "TP" si tu as créé un utilisateur spécifique
$password = "root";  // Mets le mot de passe MySQL
$dbname = "cametard";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}
?>
