<?php
include 'db_connect.php';
session_start();

try {
    // Vérifier si l'utilisateur est connecté
    if (!isset($_SESSION['user_id'])) {
        throw new Exception("Utilisateur non connecté.");
    }

    // Récupérer l'ID de la fiche à valider
    if (!isset($_POST['fiche_id'])) {
        throw new Exception("ID de fiche non spécifié.");
    }

    $ficheId = $_POST['fiche_id'];
    $userId = $_SESSION['user_id'];

    // Vérifier si la fiche existe et appartient à l'utilisateur
    $checkFiche = $conn->prepare("SELECT * FROM fiches WHERE id = ? AND utilisateur_id = ?");
    $checkFiche->bind_param("ii", $ficheId, $userId);
    $checkFiche->execute();
    $result = $checkFiche->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("Fiche non trouvée ou vous n'avez pas les autorisations nécessaires.");
    }

    $checkFiche->close();

    // Mettre à jour le statut de la fiche à 'validée'
    $query = "UPDATE fiches SET statut = 'validée' WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $ficheId);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'La fiche a été validée avec succès.']);
    } else {
        throw new Exception("Erreur lors de la mise à jour du statut de la fiche.");
    }

    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?>
