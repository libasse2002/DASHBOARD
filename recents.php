<?php
$servername = "localhost";
$username = "root";
$password = "passer123";
$dbname = "polytechd1";
$dbport = 3308;

// Connexion à la base de données
$conn = new mysqli($servername, $username, $password, $dbname, $dbport);
if ($conn->connect_error) {
  die("Connexion échouée : " . $conn->connect_error);
}

// Sélection des dernières déclarations
$sql = "SELECT department, nom_ec, hours_cm, hours_td, hours_tp, date, statut FROM fiches ORDER BY date DESC LIMIT 10";
$result = $conn->query($sql);

$recentDeclarations = [];
if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    $recentDeclarations[] = $row;
  }
}

echo json_encode($recentDeclarations);

$conn->close();
?>
