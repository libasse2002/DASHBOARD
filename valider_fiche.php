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

    // Vérifier que les paramètres sont spécifiés
    if (!$ficheId || !$statut) {
        throw new Exception("ID de fiche ou statut non spécifié.");
    }

    // Valider le statut
    $validStatuses = ['en_attente', 'validée', 'refusée']; // Liste des statuts valides
    if (!in_array($statut, $validStatuses)) {
        throw new Exception("Statut invalide spécifié.");
    }

    $userId = $_SESSION['user_id'];

    // Vérifiez si l'utilisateur est chef de département
    $isChefDepartementQuery = $conn->prepare("SELECT COUNT(*) FROM chef_departement WHERE utilisateur_id = ?");
    $isChefDepartementQuery->bind_param("i", $userId);
    $isChefDepartementQuery->execute();
    $isChefDepartementResult = $isChefDepartementQuery->get_result();
    $isChefDepartement = $isChefDepartementResult->fetch_row()[0] > 0;
    $isChefDepartementQuery->close();

    // Vérifiez si la fiche existe
    $checkFiche = $conn->prepare("SELECT * FROM fiches WHERE id = ?");
    $checkFiche->bind_param("i", $ficheId);
    $checkFiche->execute();
    $result = $checkFiche->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("Fiche non trouvée.");
    }

    $fiche = $result->fetch_assoc();
    $checkFiche->close();

    // Si l'utilisateur n'est pas chef de département, il ne peut pas changer le statut
    if (!$isChefDepartement) {
        throw new Exception("Vous n'avez pas les autorisations nécessaires pour valider cette fiche.");
    }

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
    http_response_code(400); // Envoie un code d'état HTTP 400 pour les erreurs
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?>
