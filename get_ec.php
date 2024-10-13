<?php
include 'db_connect.php';

if (isset($_GET['semestre_id']) && isset($_GET['niveau_id']) && isset($_GET['filiere_id'])) {
    $semestreId = intval($_GET['semestre_id']);
    $niveauId = intval($_GET['niveau_id']);
    $filiereId = intval($_GET['filiere_id']);

    $stmt = $conn->prepare("SELECT id, nom_ec FROM ec WHERE semestre_id = ? AND niveau_id = ? AND filiere_id = ?");
    $stmt->bind_param("iii", $semestreId, $niveauId, $filiereId);
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $ecs = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($ecs);
    } else {
        echo json_encode(['error' => 'Erreur lors de la récupération des EC.']);
    }
} else {
    echo json_encode(['error' => 'ID de semestre, niveau ou filière manquant.']);
}
?>
