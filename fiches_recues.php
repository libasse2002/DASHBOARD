<?php
include 'db_connect.php';
session_start();

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: log.php'); // Redirigez vers la page de connexion si non connecté
    exit();
}

// Récupérer les fiches reçues
$query = "SELECT f.*, d.nom AS departement_name FROM fiches f JOIN departements d ON f.departement_id = d.id WHERE f.statut = 'en_attente'";
$result = mysqli_query($conn, $query);
$fichesParDepartement = [];
while ($row = mysqli_fetch_assoc($result)) {
$fichesParDepartement[$row['departement_name']][] = $row;}
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
            <li><a href="dashboard.php"><i class='bx bxs-home bx-tada' ></i><span>Accueil</span></a></li>
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
    <!-- Conteneur pour les messages de confirmation -->
    <div id="confirmation-message" style="display: none;"></div>
    <!-- CONTENT -->
    <section id="content">
        <!-- NAVBAR -->
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
        <!-- END NAVBAR -->

        <!-- MAIN CONTENT -->
        <main>
            <div class="head-title1">
                <h1>Fiches Reçues</h1>
            </div>

            <div class="card-container">
            <?php foreach ($fichesParDepartement as $departement => $fiches): ?>
                <h2><?= htmlspecialchars($departement); ?></h2>
                <?php foreach ($fiches as $row): // Use foreach to iterate over fiches ?>
                    <?php 
                    $totalTP = ($row['hours_cm'] * 2.16) + ($row['hours_td'] * 1.37);
                    ?>
                    <div class="card" data-status="<?= htmlspecialchars($row['statut']); ?>">
                        <h3><?= htmlspecialchars($row['nom_ec']); ?></h3>
                        <p><strong>Département:</strong> <?= htmlspecialchars($row['departement_name']); ?></p>
                        <p><strong>Heures CM:</strong> <?= htmlspecialchars($row['hours_cm']); ?></p>
                        <p><strong>Heures TD:</strong> <?= htmlspecialchars($row['hours_td']); ?></p>
                        <p><strong>Heures TP:</strong> <?= htmlspecialchars($row['hours_tp']); ?></p>
                        <p><strong>Heures Totales TP:</strong> <?= number_format($totalTP, 2); ?></p>
                        <p><strong>Date:</strong> <?= htmlspecialchars($row['date']); ?></p>
                        <p><strong>Statut:</strong> <?= htmlspecialchars($row['statut']); ?></p>

                        <div class="actions">
                            <?php if ($row['statut'] == 'en_attente') { ?>
                                <button class="validate-button" data-id="<?= $row['id']; ?>">Valider</button>
                                <button class="reject-button" data-id="<?= $row['id']; ?>">Refuser</button>
                            <?php } ?>
                        </div>
                    </div>
                <?php endforeach; // End foreach for fiches ?>
            <?php endforeach; // End foreach for departements ?>
            </div>
        </main>
        <!-- END MAIN CONTENT -->
    </section>
    <!-- END CONTENT -->

    <script>
        // $(document).ready(function() {
        //     // AJAX pour valider une fiche
        //     $('.validate-button').on('click', function() {
        //         let ficheId = $(this).data('id');
        //         $.post('valider_fiche.php', { fiche_id: ficheId }, function(response) {
        //             alert(response.message);
        //             location.reload(); // Recharger la page après validation
        //         }, 'json');
        //     });

        //     // AJAX pour refuser une fiche
        //     $('.reject-button').on('click', function() {
        //         let ficheId = $(this).data('id');
        //         $.post('refuser_fiche.php', { fiche_id: ficheId }, function(response) {
        //             alert(response.message);
        //             location.reload(); // Recharger la page après refus
        //         }, 'json');
        //     });

        //     // // Redirection pour modifier une fiche
        //     // $('.modify-button').on('click', function() {
        //     //     let ficheId = $(this).data('id');
        //     //     window.location.href = 'modifier_fiche.php?id=' + ficheId;
        //     // });
        // });

        $(document).ready(function() {
    // AJAX pour valider une fiche
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
                location.reload(); // Recharger la page après validation
            },
            error: function(xhr, status, error) {
                console.error("Erreur: " + error);
                alert("Une erreur s'est produite lors de la validation.");
            }
        });
    });

    // AJAX pour refuser une fiche
    $('.reject-button').on('click', function() {
        let ficheId = $(this).data('id');
        $.post('refuser_fiche.php', { fiche_id: ficheId }, function(response) {
            alert(response.message);
            location.reload(); // Recharger la page après refus
        }, 'json');
    });
});

        // TOGGLE SIDEBAR
        const menuBar = document.querySelector('#content nav .bx.bx-menu');
        const sidebar = document.getElementById('sidebar');

        menuBar.addEventListener('click', function () {
	        sidebar.classList.toggle('hide');
        })
        document.querySelectorAll('.validate-button, .reject-button, .modify-button').forEach(button => {
        button.addEventListener('click', () => {
            button.classList.add('clicked');
            setTimeout(() => button.classList.remove('clicked'), 150);
        });
    });
    document.querySelectorAll('.card').forEach(card => {
    const status = card.getAttribute('data-status'); // Supposons que le statut soit défini dans un attribut 'data-status'
    
    if (status === 'validée') {
        card.classList.add('validée');
    } else if (status === 'en_attente') {
        card.classList.add('en_attente');
    } else if (status === 'refusée') {
        card.classList.add('refusée');
    }
});
document.addEventListener('DOMContentLoaded', function() {
    const validateButtons = document.querySelectorAll('.validate-button');
    const rejectButtons = document.querySelectorAll('.reject-button');

    validateButtons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            const card = button.closest('.card'); // Récupérer la carte parente
            card.style.backgroundColor = 'var(--light-green)'; // Changer le fond en light-green
        });

        button.addEventListener('mouseleave', function() {
            const card = button.closest('.card');
            card.style.backgroundColor = ''; // Réinitialiser à la couleur d'origine
        });
    });

    rejectButtons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            const card = button.closest('.card'); // Récupérer la carte parente
            card.style.backgroundColor = 'var(--light-red)'; // Changer le fond en light-red
        });

        button.addEventListener('mouseleave', function() {
            const card = button.closest('.card');
            card.style.backgroundColor = ''; // Réinitialiser à la couleur d'origine
        });
    });
});
    </script>
</body>
</html>
