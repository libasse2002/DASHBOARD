<?php
include 'db_connect.php'; // Connexion à la base de données
session_start();
$userId = $_SESSION['user_id'];

// Récupérer la liste des départements
$query = "SELECT id, nom FROM departements";
$result = $conn->query($query);
$departements = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nouvelle Soumission</title>
    <link rel="stylesheet" href="subm.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="submission-content">
            <h2 class="title">Nouvelle Soumission</h2>
            <form id="submissionForm">
                <input type="hidden" name="submit" value="1"> <!-- Champ caché -->

                <div class="input-div one">
                    <div class="i">
                        <i class="fas fa-building"></i>
                    </div>
                    <div>
                        <h5>Département</h5>
                        <select id="department" name="department" required>
                            <option value="">Sélectionnez un département</option>
                            <?php foreach ($departements as $dept): ?>
                                <option value="<?php echo $dept['id']; ?>"><?php echo htmlspecialchars($dept['nom']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="input-div">
                    <div class="i">
                        <i class="fas fa-book"></i>
                    </div>
                    <div>
                        <h5>Nom EC</h5>
                        <input type="text" id="nomEc" name="nomEc" required>
                    </div>
                </div>

                <div class="input-div">
                    <div class="i">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <h5>Heures de Cours Magistral</h5>
                        <input type="number" step="0.01" id="hoursCM" name="hoursCM" required>
                    </div>
                </div>

                <div class="input-div">
                    <div class="i">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <h5>Heures de Cours TD</h5>
                        <input type="number" step="0.01" id="hoursTD" name="hoursTD" required>
                    </div>
                </div>

                <div class="input-div">
                    <div class="i">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <h5>Heures de Cours TP</h5>
                        <input type="number" step="0.01" id="hoursTP" name="hoursTP" required>
                    </div>
                </div>

                <div class="input-div">
                    <div class="i">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div>
                        <h5>Date</h5>
                        <input type="date" id="date" name="date" required>
                    </div>
                </div>

                <div class="input-div">
                    <div class="i">
                        <i class="fas fa-signature"></i>
                    </div>
                    <div>
                        <h5>Signature</h5>
                        <input type="text" id="signature" name="signature" required>
                    </div>
                </div>

                <button type="submit" class="btn">Soumettre</button>
            </form>
        </div>
    </div>

    <script>
      document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('submissionForm');

    form.addEventListener('submit', function(event) {
        event.preventDefault(); // Empêche le rechargement de la page

        const formData = new FormData(form);

        fetch('submit.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                toastr.success(data.message);
                form.reset(); // Réinitialise le formulaire
                setTimeout(() => {
                    window.location.href = 'dashboard.php'; // Redirige vers le tableau de bord après 2 secondes
                }, 2000);
            } else {
                toastr.error(data.message);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            toastr.error('Une erreur est survenue. Veuillez réessayer.');
        });
    });
});
        // const form = document.getElementById('submissionForm');

        // form.addEventListener('submit', function(event) {
        //     event.preventDefault(); // Empêche le rechargement de la page

        //     const formData = new FormData(form);

        //     fetch('submit.php', {
        //         method: 'POST',
        //         body: formData
        //     })
        //     .then(response => response.json())
        //     .then(data => {
        //         if (data.success) {
        //             toastr.success('Votre soumission a été enregistrée avec succès.');
        //             form.reset(); // Réinitialise le formulaire
        //         } else {
        //             toastr.error('Erreur lors de l\'envoi de la soumission: ' + data.message);
        //         }
        //     })
        //     .catch(error => {
        //         console.error('Erreur:', error);
        //         toastr.error('Une erreur est survenue. Veuillez réessayer.');
        //     });
        // });
    </script>
</body>
</html>
