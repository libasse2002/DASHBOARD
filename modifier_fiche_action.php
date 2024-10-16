<?php
include 'db_connect.php';
session_start();

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: log.php'); // Redirige vers la page de connexion si non connecté
    exit();
}

$userId = $_SESSION['user_id'];

// Récupérer l'ID de la fiche à modifier
$ficheId = isset($_POST['fiche_id']) ? intval($_POST['fiche_id']) : 0;

if ($ficheId === 0) {
    header('Location: mes_fiches.php'); // Redirige si aucun ID n'est fourni
    exit();
}

// Récupérer les informations envoyées par le formulaire
$departmentId = isset($_POST['department']) ? intval($_POST['department']) : 0;
$filiereId = isset($_POST['filiere']) ? intval($_POST['filiere']) : 0;
$niveauId = isset($_POST['niveau']) ? intval($_POST['niveau']) : 0;
$semestreId = isset($_POST['semestre']) ? intval($_POST['semestre']) : 0;
$ecId = isset($_POST['ec']) ? intval($_POST['ec']) : 0;
$hoursCM = isset($_POST['hoursCM']) ? floatval($_POST['hoursCM']) : 0;
$hoursTD = isset($_POST['hoursTD']) ? floatval($_POST['hoursTD']) : 0;
$hoursTP = isset($_POST['hoursTP']) ? floatval($_POST['hoursTP']) : 0;
$signature = isset($_POST['signature']) ? htmlspecialchars($_POST['signature']) : '';

// Mettre à jour la fiche dans la base de données et définir le statut à 'en attente'
$updateQuery = "
    UPDATE fiches 
    SET departement_id = ?, filiere_id = ?, niveau_id = ?, semestre_id = ?, ec_id = ?, 
        hours_cm = ?, hours_td = ?, hours_tp = ?, signature = ?, statut = 'en_attente'
    WHERE id = ? AND utilisateur_id = ? AND (statut = 'en_attente' OR statut = 'refusée')
";

$stmt = $conn->prepare($updateQuery);

// Update bind_param to match the number of variables
$stmt->bind_param('iiiiiiddssi', $departmentId, $filiereId, $niveauId, $semestreId, $ecId, $hoursCM, $hoursTD, $hoursTP, $signature, $ficheId, $userId);

if ($stmt->execute()) {
    // Redirection après succès
    header('Location: mes_fiches.php?message=Fiche mise à jour avec succès.');
} else {
    // Gestion des erreurs
    echo "Erreur lors de la mise à jour de la fiche : " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
