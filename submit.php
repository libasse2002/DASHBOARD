<?php
// Fonction de logging
function logMessage($message) {
    error_log(date('[Y-m-d H:i:s] ') . $message);
}

header('Content-Type: application/json');
include 'db_connect.php';
session_start();

logMessage("Début du traitement de la soumission");
logMessage("Données POST reçues : " . print_r($_POST, true));

try {
    if (!isset($_SESSION['user_id'])) {
        throw new Exception("Utilisateur non connecté.");
    }

    // Récupération et validation des données
    $userId = $_SESSION['user_id'];
    $departmentId = filter_input(INPUT_POST, 'department', FILTER_VALIDATE_INT);
    $nomEc = filter_input(INPUT_POST, 'nomEc', FILTER_SANITIZE_STRING);
    $hoursCM = filter_input(INPUT_POST, 'hoursCM', FILTER_VALIDATE_INT);
    $hoursTD = filter_input(INPUT_POST, 'hoursTD', FILTER_VALIDATE_INT);
    $hoursTP = filter_input(INPUT_POST, 'hoursTP', FILTER_VALIDATE_INT);
    $date = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_STRING);
    $signature = filter_input(INPUT_POST, 'signature', FILTER_SANITIZE_STRING);

    logMessage("Données validées : " . json_encode([
        'userId' => $userId,
        'departmentId' => $departmentId,
        'nomEc' => $nomEc,
        'hoursCM' => $hoursCM,
        'hoursTD' => $hoursTD,
        'hoursTP' => $hoursTP,
        'date' => $date,
        'signature' => $signature
    ]));

    if ($departmentId === false || $departmentId === null) {
        throw new Exception("ID de département invalide.");
    }

    // Vérifier si le département existe
    $checkDept = $conn->prepare("SELECT id FROM departements WHERE id = ?");
    $checkDept->bind_param("i", $departmentId);
    $checkDept->execute();
    $result = $checkDept->get_result();
    if ($result->num_rows === 0) {
        throw new Exception("Le département sélectionné n'existe pas.");
    }
    $checkDept->close();

    logMessage("Département vérifié et existe");

    // Requête d'insertion dans la base de données
    $query = "INSERT INTO fiches (utilisateur_id, departement_id, nom_ec, hours_cm, hours_td, hours_tp, date, signature, statut) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'en_attente')";
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        throw new Exception("Préparation de la requête échouée : " . $conn->error);
    }

    $stmt->bind_param("iisiiiss", $userId, $departmentId, $nomEc, $hoursCM, $hoursTD, $hoursTP, $date, $signature);
    
    logMessage("Requête préparée : " . $query);
    logMessage("Paramètres liés : " . json_encode([$userId, $departmentId, $nomEc, $hoursCM, $hoursTD, $hoursTP, $date, $signature]));

    if ($stmt->execute()) {
        logMessage("Insertion réussie");
        echo json_encode(['success' => true, 'message' => 'Votre soumission a été enregistrée avec succès.']);
        header("Location: dashboard.php");
    exit();
    } else {
        throw new Exception("Erreur d'insertion dans la base de données : " . $stmt->error);
    }

    $stmt->close();
} catch (Exception $e) {
    logMessage("Erreur : " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
logMessage("Fin du traitement de la soumission");
?>