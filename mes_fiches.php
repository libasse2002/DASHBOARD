<?php
include 'db_connect.php';
session_start();

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: log.php'); // Redirigez vers la page de connexion si non connecté
    exit();
}

$userId = $_SESSION['user_id'];

// Vérifiez si l'utilisateur est un chef de département
$isChefDepartementQuery = $conn->prepare("SELECT COUNT(*) FROM chef_departement WHERE utilisateur_id = ?");
$isChefDepartementQuery->bind_param("i", $userId);
$isChefDepartementQuery->execute();
$isChefDepartementResult = $isChefDepartementQuery->get_result();
$isChefDepartement = $isChefDepartementResult->fetch_row()[0] > 0;
$isChefDepartementQuery->close();

// Requête mise à jour pour inclure le niveau, le semestre, l'EC et la filière
$query = "
    SELECT f.*, d.nom AS departement_name, n.nom AS niveau_name, s.nom AS semestre_name, e.nom_ec, fi.nom AS filiere_name
    FROM fiches f 
    JOIN departements d ON f.departement_id = d.id 
    LEFT JOIN niveaux n ON f.niveau_id = n.id 
    LEFT JOIN semestres s ON f.semestre_id = s.id 
    LEFT JOIN ec e ON f.ec_id = e.id
    LEFT JOIN filieres fi ON f.filiere_id = fi.id 
    WHERE f.utilisateur_id = $userId
";
$result = mysqli_query($conn, $query);

// Regroupement des fiches par département
$fichesParDepartement = [];
while ($row = mysqli_fetch_assoc($result)) {
    $fichesParDepartement[$row['departement_name']][] = $row;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes Fiches</title>
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"/>
    <link rel="stylesheet" href="dashboard1.css">
</head>
<body>
    <!-- SIDEBAR -->
    <section id="sidebar">
        <a href="#" class="brand">
            <img src="UAM1.png" id="uam" alt="">
            <span class="text">Polytech Diamniadio</span>
        </a>
        <ul class="side-menu top">
            <li><a href="dashboard.php"><i class='bx bxs-home bx-tada'></i><span>Accueil</span></a></li>
            <li class="active"><a href="mes_fiches.php"><i class='bx bxs-collection bx-tada'></i><span>Mes Fiches</span></a></li>
            <?php if ($isChefDepartement): ?>
                <li><a href="fiches_recues.php"><i class='bx bxs-file-import bx-tada'></i><span>Fiches Reçues</span></a></li>
            <?php endif; ?>
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
            <div class="head-title1">
                <h1>Mes Fiches</h1>
            </div>

            <div class="card-container">
                <?php foreach ($fichesParDepartement as $departement => $fiches): ?>
                    <h2><?= htmlspecialchars($departement); ?></h2>
                    <?php foreach ($fiches as $row): 
                        $totalTP = ($row['hours_cm'] * 2.16) + ($row['hours_td'] * 1.37);
                    ?>
                        <div class="card" data-status="<?= htmlspecialchars($row['statut']); ?>">
                            <h3><?= htmlspecialchars($row['filiere_name']); ?></h3>
                            <p><strong>Département:</strong> <?= htmlspecialchars($row['departement_name']); ?></p>
                            <p><strong>Niveau:</strong> <?= htmlspecialchars($row['niveau_name']); ?></p>
                            <p><strong>Semestre:</strong> <?= htmlspecialchars($row['semestre_name']); ?></p>
                            <p><strong>EC:</strong> <?= htmlspecialchars($row['nom_ec']); ?></p>
                            <p><strong>Heures CM:</strong> <?= htmlspecialchars($row['hours_cm']); ?></p>
                            <p><strong>Heures TD:</strong> <?= htmlspecialchars($row['hours_td']); ?></p>
                            <p><strong>Heures TP:</strong> <?= htmlspecialchars($row['hours_tp']); ?></p>
                            <p><strong>Heures Totales TP:</strong> <?= number_format($totalTP, 2); ?></p>
                            <p><strong>Date:</strong> <?= htmlspecialchars($row['date']); ?></p>
                            <p><strong>Statut:</strong> <?= htmlspecialchars($row['statut']); ?></p>
                            
                            <?php if ($row['statut'] == 'en_attente' || $row['statut'] == 'refusée') { ?>
                                <a href="modifier_fiche.php?id=<?= $row['id']; ?>" class="modify-button">Modifier</a>
                            <?php } ?>
                        </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </div>
        </main>
        <!-- END MAIN CONTENT -->
    </section>
    <!-- END CONTENT -->

    <script src="dashboard.js"></script>
</body>
</html>
