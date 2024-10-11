<?php
include 'db_connect.php';
session_start();

try {
    // Vérifiez si l'utilisateur est connecté
    if (!isset($_SESSION['user_id'])) {
        throw new Exception("Utilisateur non connecté.");
    }

    // Récupérer les données de la requête
    $data = json_decode(file_get_contents("php://input"), true);
    $ficheId = $data['fiche_id'] ?? null;
    $statut = $data['statut'] ?? null;

    if (!$ficheId || !$statut) {
        throw new Exception("ID de fiche ou statut non spécifié.");
    }

    $userId = $_SESSION['user_id'];

    // Vérifiez si la fiche existe et appartient à l'utilisateur
    $checkFiche = $conn->prepare("SELECT * FROM fiches WHERE id = ? AND utilisateur_id = ?");
    $checkFiche->bind_param("ii", $ficheId, $userId);
    $checkFiche->execute();
    $result = $checkFiche->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("Fiche non trouvée ou vous n'avez pas les autorisations nécessaires.");
    }

    $checkFiche->close();

    // Mettre à jour le statut de la fiche
    $query = "UPDATE fiches SET statut = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $statut, $ficheId);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => "La fiche a été mise à jour en tant que '$statut' avec succès."]);
    } else {
        throw new Exception("Erreur lors de la mise à jour du statut de la fiche.");
    }

    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?>
