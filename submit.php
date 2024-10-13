<?php
include 'db_connect.php';
session_start();

if (isset($_POST['submit'])) {
    $utilisateurId = $_SESSION['user_id']; // Identifiant de l'utilisateur qui soumet
    $departmentId = intval($_POST['department']);
    $filiereId = intval($_POST['filiere']);
    $niveauId = intval($_POST['niveau']);
    $semestreId = intval($_POST['semestre']);
    $ecId = intval($_POST['ec']);
    $hoursCM = floatval($_POST['hoursCM']);
    $hoursTD = floatval($_POST['hoursTD']);
    $hoursTP = floatval($_POST['hoursTP']);
    $signature = $_POST['signature'];

    // Insertion dans la table fiches
    $stmt = $conn->prepare("INSERT INTO fiches (utilisateur_id, departement_id, filiere_id, niveau_id, semestre_id, ec_id, hours_cm, hours_td, hours_tp, signature) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiiidddds", $utilisateurId, $departmentId, $filiereId, $niveauId, $semestreId, $ecId, $hoursCM, $hoursTD, $hoursTP, $signature);

    if ($stmt->execute()) {
        // Récupérer l'utilisateur_id du chef de département
        $queryChef = "SELECT utilisateur_id FROM chef_departement WHERE departement_id = ?";
        $stmtChef = $conn->prepare($queryChef);
        $stmtChef->bind_param("i", $departmentId);
        $stmtChef->execute();
        $resultChef = $stmtChef->get_result();
        $chef = $resultChef->fetch_assoc();

        if ($chef) {
            // Récupérer l'adresse e-mail de l'utilisateur correspondant
            $queryEmail = "SELECT email FROM utilisateur2 WHERE id = ?";
            $stmtEmail = $conn->prepare($queryEmail);
            $stmtEmail->bind_param("i", $chef['utilisateur_id']);
            $stmtEmail->execute();
            $resultEmail = $stmtEmail->get_result();
            $user = $resultEmail->fetch_assoc();

            if ($user) {
                $to = $user['email'];
                $subject = "Nouvelle Soumission de Chargé d'Enseignement";
                $message = "Une nouvelle soumission a été faite pour le département : " . htmlspecialchars($departmentId) . ".\n\nDétails :\n- Filière : " . htmlspecialchars($filiereId) . "\n- Niveau : " . htmlspecialchars($niveauId) . "\n- Semestre : " . htmlspecialchars($semestreId) . "\n- EC : " . htmlspecialchars($ecId) . "\n\nVeuillez consulter le tableau de bord pour plus de détails.";
                $headers = "From: dialimamoulaye@uam.edu.sn"; // Remplacez par votre adresse d'envoi

                // Envoyer l'e-mail
                if (mail($to, $subject, $message, $headers)) {
                    // E-mail envoyé avec succès
                    $_SESSION['success'] = 'Soumission réussie! Un e-mail a été envoyé au chef de département.';
                } else {
                    // Échec de l'envoi de l'e-mail
                    $_SESSION['error'] = 'Soumission réussie, mais échec de l\'envoi de l\'e-mail au chef de département.';
                }
            } else {
                $_SESSION['error'] = 'Soumission réussie, mais aucun e-mail trouvé pour le chef de département.';
            }
        } else {
            $_SESSION['error'] = 'Soumission réussie, mais aucun chef de département trouvé pour ce département.';
        }

        header("Location: dashboard.php");
        exit();
    } else {
        $_SESSION['error'] = 'Erreur lors de la soumission. Veuillez réessayer.';
    }
}
?>
