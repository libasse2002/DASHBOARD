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

// Requête pour récupérer les fiches
$query = "
    SELECT f.*, d.nom AS departement_name, e.nom_ec, fi.nom AS filiere_name, u.username AS user_name 
    FROM fiches f 
    JOIN departements d ON f.departement_id = d.id 
    JOIN ec e ON f.ec_id = e.id 
    JOIN filieres fi ON f.filiere_id = fi.id 
    JOIN utilisateur2 u ON f.utilisateur_id = u.id
    WHERE f.statut = 'en_attente' AND f.departement_id = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $departementId);
$stmt->execute();
$result = $stmt->get_result();

$fichesParDepartement = [];
if ($result->num_rows === 0) {
    echo "Aucune fiche trouvée.";
} else {
    while ($row = $result->fetch_assoc()) {
        $fichesParDepartement[$row['departement_name']][] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Fiches Reçues</title>
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"/>
    <link rel="stylesheet" href="dashboard1.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
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
            <li><a href="mes_fiches.php"><i class='bx bxs-collection bx-tada'></i><span>Mes Fiches</span></a></li>
            <li class="active"><a href="fiches_recues.php"><i class='bx bxs-file-import bx-tada'></i><span>Fiches Reçues</span></a></li>
            <li><a href="fiches_validées.php"><i class='bx bxs-select-multiple bx-tada'></i><span>Fiches Validées</span></a></li>
            <li><a href="nouvelle_soumission.php"><i class='bx bxs-file-export bx-tada'></i><span>Nouvelle Soumission</span></a></li>
        </ul>
        <ul class="side-menu">
            <li><a href="#"><i class='bx bxs-help-circle bx-tada'></i><span>Aide</span></a></li>
            <li><a href="logout.php" class="logout"><i class='bx bxs-log-out-circle'></i><span>Déconnexion</span></a></li>
        </ul>
    </section>
    <!-- END SIDEBAR -->

    <section id="content">
        <nav>
            <i class='bx bx-menu'></i>
            <form action="#">
                <div class="form-input">
                    <input type="search" placeholder="Recherche...">
                    <button type="submit" class="search-button"><i class='bx bx-search'></i></button>
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

        <main>
            <div class="head-title1">
                <h1>Fiches Reçues</h1>
            </div>

            <div class="card-container">
                <?php foreach ($fichesParDepartement as $departement => $fiches): ?>
                    <h2><?= htmlspecialchars($departement); ?></h2>
                    <?php foreach ($fiches as $row): ?>
                        <?php 
                        $totalTP = (($row['hours_cm'] ?? 0) * 2.16) + (($row['hours_td'] ?? 0) * 1.37); 
                        ?>
                        <div class="card" data-status="<?= htmlspecialchars($row['statut'] ?? ''); ?>">
                            <h3><?= htmlspecialchars($row['filiere_name'] ?? ''); ?></h3>
                            <p><strong>Département:</strong> <?= htmlspecialchars($row['departement_name'] ?? ''); ?></p>
                            <p><strong>EC:</strong> <?= htmlspecialchars($row['nom_ec'] ?? ''); ?></p>
                            <p><strong>Heures CM:</strong> <?= htmlspecialchars($row['hours_cm'] ?? 0); ?></p>
                            <p><strong>Heures TD:</strong> <?= htmlspecialchars($row['hours_td'] ?? 0); ?></p>
                            <p><strong>Heures TP:</strong> <?= htmlspecialchars($row['hours_tp'] ?? 0); ?></p>
                            <p><strong>Heures Totales TP:</strong> <?= number_format($totalTP, 2); ?></p>
                            <p><strong>Date:</strong> <?= htmlspecialchars($row['date'] ?? ''); ?></p>
                            <p><strong>Statut:</strong> <?= htmlspecialchars($row['statut'] ?? ''); ?></p>
                            <p><strong>Soumis par:</strong> <?= htmlspecialchars($row['user_name'] ?? ''); ?></p>
                            
                            <div class="actions">
                                <i class="bx bxs-check-circle validate-button" data-id="<?= $row['id']; ?>" title="Valider"></i>
                                <i class="bx bxs-x-circle reject-button" data-id="<?= $row['id']; ?>" title="Refuser"></i>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </div>
        </main>
    </section>

    <script>
        $(document).ready(function() {
            $('.validate-button').on('click', function() {
                let ficheId = $(this).data('id');
                $.ajax({
                    url: 'valider_fiche.php',
                    type: 'POST',
                    contentType: 'application/json',
                    dataType: 'json',
                    data: JSON.stringify({ fiche_id: ficheId, statut: 'validée' }),
                    success: function(response) {
                        alert(response.message);
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        console.error("Erreur: " + error);
                        alert("Une erreur s'est produite lors de la validation.");
                    }
                });
            });

            $('.reject-button').on('click', function() {
                let ficheId = $(this).data('id');
                $.post('refuser_fiche.php', { fiche_id: ficheId }, function(response) {
                    alert(response.message);
                    location.reload();
                }, 'json');
            });

            const menuBar = document.querySelector('#content nav .bx.bx-menu');
            const sidebar = document.getElementById('sidebar');

            menuBar.addEventListener('click', function () {
                sidebar.classList.toggle('hide');
            });

            document.querySelectorAll('.validate-button, .reject-button').forEach(button => {
                button.addEventListener('click', () => {
                    button.classList.add('clicked');
                    setTimeout(() => button.classList.remove('clicked'), 150);
                });
            });

            document.querySelectorAll('.card').forEach(card => {
                const status = card.getAttribute('data-status');
                
                if (status === 'validée') {
                    card.classList.add('validée');
                } else if (status === 'en_attente') {
                    card.classList.add('en-attente');
                } else if (status === 'refusée') {
                    card.classList.add('refusée');
                }
            });

            const validateButtons = document.querySelectorAll('.validate-button');
            const rejectButtons = document.querySelectorAll('.reject-button');

            validateButtons.forEach(button => {
                button.addEventListener('mouseenter', function() {
                    const card = button.closest('.card');
                    card.style.backgroundColor = 'var(--light-green)';
                    const textElements = card.querySelectorAll('p, h3');
                    textElements.forEach(text => text.style.color = 'var(--green)');
                });

                button.addEventListener('mouseleave', function() {
                    const card = button.closest('.card');
                    card.style.backgroundColor = '';
                    const textElements = card.querySelectorAll('p, h3');
                    textElements.forEach(text => text.style.color = 'var(--blue)');
                });
            });

            rejectButtons.forEach(button => {
                button.addEventListener('mouseenter', function() {
                    const card = button.closest('.card');
                    card.style.backgroundColor = 'var(--light-red)';
                    const textElements = card.querySelectorAll('p, h3');
                    textElements.forEach(text => text.style.color = 'var(--red2)');
                });

                button.addEventListener('mouseleave', function() {
                    const card = button.closest('.card');
                    card.style.backgroundColor = '';
                    const textElements = card.querySelectorAll('p, h3');
                    textElements.forEach(text => text.style.color = 'var(--blue)');
                });
            });
        });
        const switchMode = document.getElementById('switch-mode');

switchMode.addEventListener('change', function () {
	if(this.checked) {
		document.body.classList.add('dark');
	} else {
		document.body.classList.remove('dark');
	}
});
    </script>
</body>
</html>
