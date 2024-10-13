<?php
include 'db_connect.php';

if (isset($_GET['departement_id'])) {
    $departementId = intval($_GET['departement_id']);

    $stmt = $conn->prepare("SELECT id, nom FROM filieres WHERE departement_id = ?");
    $stmt->bind_param("i", $departementId);
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $filieres = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($filieres);
    } else {
        echo json_encode(['error' => 'Erreur lors de la récupération des filières.']);
    }
} else {
    echo json_encode(['error' => 'ID de département manquant.']);
}
?>
