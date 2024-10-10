<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Vous devez être connecté pour accéder à cette page.");
}

// Récupérer l'ID de la fiche à partir de l'URL
$ficheId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$ficheId) {
    die("ID de fiche invalide.");
}

// Récupérer les détails de la fiche
$query = "SELECT * FROM fiches WHERE id = ? AND utilisateur_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $ficheId, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Fiche introuvable.");
}

$fiche = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Fiche</title>
    <link rel="stylesheet" href="dashboard1.css">
</head>
<body>
    <div class="content">
        <h2>Modifier Fiche</h2>
        <form action="modifier_fiche_action.php" method="POST">
            <input type="hidden" name="fiche_id" value="<?= $fiche['id']; ?>">

            <label for="nomEc">Nom de l'EC :</label>
            <input type="text" id="nomEc" name="nomEc" value="<?= htmlspecialchars($fiche['nom_ec']); ?>" required>

            <label for="hoursCM">Heures CM :</label>
            <input type="number" step="0.01" id="hoursCM" name="hoursCM" value="<?= $fiche['hours_cm']; ?>" required>

            <label for="hoursTD">Heures TD :</label>
            <input type="number" step="0.01" id="hoursTD" name="hoursTD" value="<?= $fiche['hours_td']; ?>" required>

            <label for="hoursTP">Heures TP :</label>
            <input type="number" step="0.01" id="hoursTP" name="hoursTP" value="<?= $fiche['hours_tp']; ?>" required>

            <label for="date">Date :</label>
            <input type="date" id="date" name="date" value="<?= $fiche['date']; ?>" required>

            <label for="signature">Signature :</label>
            <input type="text" id="signature" name="signature" value="<?= htmlspecialchars($fiche['signature']); ?>" required>

            <button type="submit">Enregistrer les modifications</button>
        </form>
    </div>
</body>
</html>
