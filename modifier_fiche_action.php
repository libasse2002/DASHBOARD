<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die(json_encode(['success' => false, 'message' => "Utilisateur non connecté."]));
}

// Récupérer et valider les données du formulaire
$ficheId = filter_input(INPUT_POST, 'fiche_id', FILTER_VALIDATE_INT);
$nomEc = filter_input(INPUT_POST, 'nomEc', FILTER_SANITIZE_STRING);
$hoursCM = filter_input(INPUT_POST, 'hoursCM', FILTER_VALIDATE_FLOAT);
$hoursTD = filter_input(INPUT_POST, 'hoursTD', FILTER_VALIDATE_FLOAT);
$hoursTP = filter_input(INPUT_POST, 'hoursTP', FILTER_VALIDATE_FLOAT);
$date = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_STRING);
$signature = filter_input(INPUT_POST, 'signature', FILTER_SANITIZE_STRING);

if (!$ficheId || !$nomEc || !$hoursCM || !$hoursTD || !$hoursTP || !$date || !$signature) {
    die(json_encode(['success' => false, 'message' => "Données invalides."]));
}

// Mettre à jour la fiche
$query = "UPDATE fiches SET nom_ec = ?, hours_cm = ?, hours_td = ?, hours_tp = ?, date = ?, signature = ? WHERE id = ? AND utilisateur_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("sdddssii", $nomEc, $hoursCM, $hoursTD, $hoursTP, $date, $signature, $ficheId, $_SESSION['user_id']);

if ($stmt->execute()) {
    header("Location: mes_fiches.php");
    exit();
} else {
    die(json_encode(['success' => false, 'message' => "Erreur de mise à jour."]));
}
