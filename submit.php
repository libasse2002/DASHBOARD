<?php
include 'db_connect.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'C:/Users/user/phpmailer/vendor/autoload.php';

session_start();

if (isset($_POST['submit'])) {
    $utilisateurId = $_SESSION['user_id'];
    $departmentId = intval($_POST['department']);
    $filiereId = intval($_POST['filiere']);
    $niveauId = intval($_POST['niveau']);
    $semestreId = intval($_POST['semestre']);
    $ecId = intval($_POST['ec']);
    $hoursCM = floatval($_POST['hoursCM']);
    $hoursTD = floatval($_POST['hoursTD']);
    $hoursTP = floatval($_POST['hoursTP']);
    $signature = $_POST['signature'];

    // Insertion de la soumission dans la table 'fiches'
    $stmt = $conn->prepare("INSERT INTO fiches (utilisateur_id, departement_id, filiere_id, niveau_id, semestre_id, ec_id, hours_cm, hours_td, hours_tp, signature) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiiidddds", $utilisateurId, $departmentId, $filiereId, $niveauId, $semestreId, $ecId, $hoursCM, $hoursTD, $hoursTP, $signature);

    if ($stmt->execute()) {
        // Récupération des noms pour le message
        $nomUtilisateur = $conn->query("SELECT nom FROM utilisateur2 WHERE id = $utilisateurId")->fetch_assoc()['nom'];
        $prenomUtilisateur = $conn->query("SELECT prenom FROM utilisateur2 WHERE id = $utilisateurId")->fetch_assoc()['prenom'];
        $nomDepartement = $conn->query("SELECT nom FROM departements WHERE id = $departmentId")->fetch_assoc()['nom'];
        $nomFiliere = $conn->query("SELECT nom FROM filieres WHERE id = $filiereId")->fetch_assoc()['nom'];
        $nomNiveau = $conn->query("SELECT nom FROM niveaux WHERE id = $niveauId")->fetch_assoc()['nom'];
        $nomSemestre = $conn->query("SELECT nom FROM semestres WHERE id = $semestreId")->fetch_assoc()['nom'];
        $nomEC = $conn->query("SELECT nom_ec FROM ec WHERE id = $ecId")->fetch_assoc()['nom_ec'];

        // Récupération de l'email du chef de département
        $queryChef = "SELECT utilisateur_id FROM chef_departement WHERE departement_id = ?";
        $stmtChef = $conn->prepare($queryChef);
        $stmtChef->bind_param("i", $departmentId);
        $stmtChef->execute();
        $resultChef = $stmtChef->get_result();
        $chef = $resultChef->fetch_assoc();

        if ($chef) {
            $queryEmail = "SELECT email FROM utilisateur2 WHERE id = ?";
            $stmtEmail = $conn->prepare($queryEmail);
            $stmtEmail->bind_param("i", $chef['utilisateur_id']);
            $stmtEmail->execute();
            $resultEmail = $stmtEmail->get_result();
            $user = $resultEmail->fetch_assoc();

            if ($user) {
                $to = $user['email'];
                $subject = "Nouvelle Soumission de Charges horaires";
                $message = "Une nouvelle soumission a été faite par : $prenomUtilisateur $nomUtilisateur.\n\nDétails :\n" .
                           "- Département : $nomDepartement\n" .
                           "- Filière : $nomFiliere\n" .
                           "- Niveau : $nomNiveau\n" .
                           "- Semestre : $nomSemestre\n" .
                           "- EC : $nomEC\n\nVeuillez consulter le tableau de bord pour plus de détails.";

                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'dia.limamoulaye@uam.edu.sn';
                    $mail->Password = 'xgtk awxw rbsl ywur';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    $mail->setFrom('dia.limamoulaye@uam.edu.sn', 'Limamoulaye Dia');
                    $mail->addAddress($to);

                    $mail->isHTML(false);
                    $mail->Subject = $subject;
                    $mail->Body    = $message;

                    $mail->send();
                    $_SESSION['success'] = 'Soumission réussie! Un e-mail a été envoyé au chef de département.';
                } catch (Exception $e) {
                    $_SESSION['error'] = 'Soumission réussie, mais échec de l\'envoi de l\'e-mail au chef de département. Erreur : ' . $mail->ErrorInfo;
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
