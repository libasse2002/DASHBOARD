<?php
$servername = "localhost";
$username = "root";
$password = "passer123";
$dbname = "polytechd";
$dbport = 3308;

// Connexion à la base de données
$conn = new mysqli($servername, $username, $password, $dbname, $dbport);
if ($conn->connect_error) {
  die("Connexion échouée : " . $conn->connect_error);
}
