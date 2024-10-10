<?php
include 'db_connect.php'; // Connexion à la base de données
session_start();

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: log.php'); // Redirigez vers la page de connexion si non connecté
    exit();
}

// Vérifiez si un ID de fiche est passé en paramètre
if (!isset($_GET['id'])) {
    echo 'ID de la fiche non fourni.';
    exit();
}

// Récupérez l'ID de la fiche
$ficheId = intval($_GET['id']);

// Récupérez les informations de la fiche
$query = "SELECT * FROM fiches WHERE id = $ficheId AND utilisateur_id = {$_SESSION['user_id']}";
$result = mysqli_query($conn, $query);

// Vérifiez si la requête a échoué
if (!$result) {
    echo 'Erreur lors de la récupération de la fiche : ' . mysqli_error($conn);
    exit();
}

// Vérifiez si la fiche existe
if (mysqli_num_rows($result) == 0) {
    echo 'Fiche non trouvée.';
    exit();
}

// Récupérez les données de la fiche
$fiche = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Détails de la Fiche</title>
  <link rel="stylesheet" href="dashboard.css">
</head>
<body>
  <div class="content">
    <h2>Détails de la Fiche</h2>
    <table>
      <tr>
        <th>Département</th>
        <td><?= htmlspecialchars($fiche['departement_id']); ?></td>
      </tr>
      <tr>
        <th>Nom EC</th>
        <td><?= htmlspecialchars($fiche['nom_ec']); ?></td>
      </tr>
      <tr>
        <th>Heures CM</th>
        <td><?= htmlspecialchars($fiche['hours_cm']); ?></td>
      </tr>
      <tr>
        <th>Heures TD</th>
        <td><?= htmlspecialchars($fiche['hours_td']); ?></td>
      </tr>
      <tr>
        <th>Heures TP</th>
        <td><?= htmlspecialchars($fiche['hours_tp']); ?></td>
      </tr>
      <tr>
        <th>Date</th>
        <td><?= htmlspecialchars($fiche['date']); ?></td>
      </tr>
      <tr>
        <th>Statut</th>
        <td><?= htmlspecialchars($fiche['statut']); ?></td>
      </tr>
      <tr>
        <th>Date de création</th>
        <td><?= htmlspecialchars($fiche['date_creation']); ?></td>
      </tr>
      <tr>
        <th>Date de modification</th>
        <td><?= htmlspecialchars($fiche['date_modification']); ?></td>
      </tr>
    </table>
    <a href="mes_fiches.php">Retour à Mes Fiches</a>
  </div>
</body>
</html>
