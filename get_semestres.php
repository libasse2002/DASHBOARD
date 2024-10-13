<?php
include 'db_connect.php';

if (isset($_GET['niveau_id'])) {
    $niveauId = intval($_GET['niveau_id']);

    $stmt = $conn->prepare("SELECT id, nom FROM semestres WHERE niveau_id = ?");
    $stmt->bind_param("i", $niveauId);
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $semestres = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($semestres);
    } else {
        echo json_encode(['error' => 'Erreur lors de la récupération des semestres.']);
    }
} else {
    echo json_encode(['error' => 'ID de niveau manquant.']);
}
?>
