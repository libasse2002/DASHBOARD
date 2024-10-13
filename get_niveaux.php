<?php
include 'db_connect.php';

if (isset($_GET['filiere_id'])) {
    $filiereId = intval($_GET['filiere_id']);

    $stmt = $conn->prepare("SELECT id, nom FROM niveaux WHERE filiere_id = ?");
    $stmt->bind_param("i", $filiereId);
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $niveaux = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($niveaux);
    } else {
        echo json_encode(['error' => 'Erreur lors de la récupération des niveaux.']);
    }
} else {
    echo json_encode(['error' => 'ID de filière manquant.']);
}
?>
