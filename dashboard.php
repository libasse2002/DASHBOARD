<?php
session_start();
include('db_connect.php'); // Assurez-vous que le chemin est correct

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: log.php'); // Redirigez vers la page de connexion si non connecté
    exit();
}

// Récupérez les informations de l'utilisateur
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM utilisateur2 WHERE id = '$user_id'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Récupérer les statistiques des fiches
$pending_query = "SELECT COUNT(*) AS count FROM fiches WHERE statut = 'en_attente' AND utilisateur_id = '$user_id'";
$approved_query = "SELECT COUNT(*) AS count FROM fiches WHERE statut = 'validée' AND utilisateur_id = '$user_id'";
$rejected_query = "SELECT COUNT(*) AS count FROM fiches WHERE statut = 'refusée' AND utilisateur_id = '$user_id'";

$pending_result = mysqli_query($conn, $pending_query);
$approved_result = mysqli_query($conn, $approved_query);
$rejected_result = mysqli_query($conn, $rejected_query);

if ($pending_result && $approved_result && $rejected_result) {
    $pending_count = mysqli_fetch_assoc($pending_result)['count'];
    $approved_count = mysqli_fetch_assoc($approved_result)['count'];
    $rejected_count = mysqli_fetch_assoc($rejected_result)['count'];
} else {
    // Gérer l'erreur si une des requêtes échoue
    echo 'Erreur lors de la récupération des données : ' . mysqli_error($conn);
}

// Récupérer les déclarations récentes
$recent_declarations_query = "SELECT * FROM fiches WHERE utilisateur_id = '$user_id' ORDER BY date DESC LIMIT 5";
$recent_declarations_result = mysqli_query($conn, $recent_declarations_query);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"/>
    <link rel="stylesheet" href="dashboard1.css">
    <title>Polytech Diamniadio</title>
</head>
<body>

    <!-- SIDEBAR -->
    <section id="sidebar">
        <a href="#" class="brand">
             <img src="UAM1.png" id="uam" alt="">
            <span class="text">Polytech Diamniadio</span>
        </a>
        <ul class="side-menu top">
            <li class="active"><a href="dashboard.php"><i class='bx bxs-home bx-tada' ></i><span>Accueil</span></a></li>
            <li><a href="mes_fiches.php"><i class='bx bxs-collection bx-tada'></i><span>Mes Fiches</span></a></li>
            <li><a href="fiches_recues.php"><i class='bx bxs-file-import bx-tada'></i><span>Fiches Reçues</span></a></li>
            <li><a href="fiches_validées.php"><i class='bx bxs-select-multiple bx-tada'></i><span>Fiches Validées</span></a></li>
            <li><a href="nouvelle_soumission.php"><i class='bx bxs-file-export bx-tada'></i><span>Nouvelle Soumission</span></a></li>
        </ul>
        <ul class="side-menu">
            <li><a href="#"><i class='bx bxs-help-circle bx-tada'></i><span>Aide</span></a></li>
            <li><a href="logout.php" class="logout"><i class='bx bxs-log-out-circle'></i><span>Déconnexion</span></a></li>
        </ul>
    </section>
    <!-- END SIDEBAR -->

    <!-- CONTENT -->
    <section id="content">
        <!-- NAVBAR -->
        <nav>
            <i class='bx bx-menu'></i>
            <form action="#">
                <div class="form-input">
                    <input type="search" placeholder="Recherche...">
                    <button type="submit" class="search-btn"><i class='bx bx-search'></i></button>
                </div>
            </form>
            <input type="checkbox" id="switch-mode" hidden>
            <label for="switch-mode" class="switch-mode"></label>
            <a href="#" class="notification">
                <i class='bx bxs-bell'></i>
                <span class="num">8</span>
            </a>
            <a href="#" class="profile"><img src="img/people.png" alt="Profil"></a>
        </nav>
        <!-- END NAVBAR -->

        <!-- MAIN CONTENT -->
        <main>
            <div class="head-title">
                <div class="left">
                    <h1>Plateforme de déclaration des charges horaires</h1>
                    <ul class="breadcrumb">
                        <li><a href="dashboard.php">Dashboard</a></li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li><a class="active" href="dashboard.php">Home</a></li>
                    </ul>
                </div>
                <a href="#" class="btn-download"><i class='bx bxs-cloud-download'></i><span>Télécharger le rapport</span></a>
            </div>

            <div class="box-info">
                <li><div class="bx" style="background-color: var(--light-green);"><i class="bx bx-hourglass bx-lg"></i></div><div class="text"><h3 id="count-pending"><?php echo $pending_count; ?></h3><p>Fiches en attente</p></div></li>
                <li><div class="bx" style="background-color: var(--light-blue);"><i class="bx bx-check-circle bx-lg"></i></div><div class="text"><h3 id="count-approved"><?php echo $approved_count; ?></h3><p>Fiches validées</p></div></li>
                <li><div class="bx" style="background-color: var(--red);"><i class="bx bx-x-circle bx-lg"></i></div><div class="text"><h3 id="count-rejected"><?php echo $rejected_count; ?></h3><p>Fiches refusées</p></div></li>
            </div>

            <div class="recent-declarations">
                <h3>Déclarations Récentes</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Départements</th>
                            <th>Date</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody id="recentDeclarationsList">
                        <?php while ($declaration = mysqli_fetch_assoc($recent_declarations_result)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($declaration['nom_ec']); ?></td>
                                <td><?php echo htmlspecialchars($declaration['date']); ?></td>
                                <td><?php echo htmlspecialchars($declaration['statut']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
        <!-- END MAIN CONTENT -->
    </section>
    <!-- END CONTENT -->

    <script src="dashboard.js"></script>
</body>
</html>
