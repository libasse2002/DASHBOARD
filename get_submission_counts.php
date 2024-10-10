<?php
// Connexion à la base de données
$host = 'localhost';
$db = 'polytechD';
$user = 'root';
$pass = 'passer123';
$port = 3308;

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Requête pour compter les fiches par statut
    $query = "SELECT status, COUNT(*) as count FROM submissions GROUP BY status";
    $stmt = $pdo->prepare($query);
    $stmt->execute();

    $data = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[$row['status']] = $row['count'];
    }

    echo json_encode($data);
} catch (PDOException $e) {
    echo 'Erreur de connexion : ' . $e->getMessage();
}
