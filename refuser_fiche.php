<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: log.php');
    exit();
}

// Récupérer l'ID de l'utilisateur connecté
$userId = $_SESSION['user_id'];

// Récupérer l'ID du département du chef de département
$queryChefDepartement = "SELECT departement_id FROM chef_departement WHERE utilisateur_id = ?";
$stmt = $conn->prepare($queryChefDepartement);
$stmt->bind_param("i", $userId);
$stmt->execute();
$resultChef = $stmt->get_result();

if ($resultChef->num_rows === 0) {
    die("Vous n'êtes pas un chef de département ou votre département n'est pas défini.");
}

$departementRow = $resultChef->fetch_assoc();
$departementId = $departementRow['departement_id'];

// Vérifier si une fiche a été soumise
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fiche_id'])) {
    $ficheId = $_POST['fiche_id'];

    // Mettre à jour le statut de la fiche à "refusée"
    $queryUpdate = "UPDATE fiches SET statut = 'refusée' WHERE id = ?";
    $stmt = $conn->prepare($queryUpdate);
    $stmt->bind_param("i", $ficheId);
    
    if ($stmt->execute()) {
        $response = [
            'status' => 'success',
            'message' => 'Fiche refusée avec succès.'
        ];
    } else {
        $response = [
            'status' => 'error',
            'message' => 'Une erreur s\'est produite lors du refus de la fiche.'
        ];
    }
    echo json_encode($response);
    exit();
}
?>
