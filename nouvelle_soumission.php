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
    <link href="https://fonts.googleapis.com/css?family=Poppins:600&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a81368914c.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
</head>
<body>
    <!-- Background Wave Image -->
    <img class="wave" src="wave3.png">
    
    <div class="container">
        <!-- Left Image (Illustration) -->
        <div class="img">
            <img src="bg.svg">
        </div>

        <!-- Submission Form Content -->
        <div class="submission-content">
            <!-- Profile Image and Title -->
            <h2 class="title">Nouvelle Soumission</h2>

            <!-- Submission Form -->
            <form id="submissionForm">
                <input type="hidden" name="submit" value="1"> <!-- Hidden Field -->

                <!-- Department Field -->
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

                <!-- Course Name Field -->
                <div class="input-div">
                    <div class="i">
                        <i class="fas fa-book"></i>
                    </div>
                    <div>
                        <h5>Nom EC</h5>
                        <input type="text" id="nomEc" name="nomEc" required>
                    </div>
                </div>

                <!-- Course Hours Fields -->
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

                <!-- Date Field -->
                <div class="input-div">
                    <div class="i">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div>
                        <h5>Date</h5>
                        <input type="date" id="date" name="date" required>
                    </div>
                </div>

                <!-- Signature Field -->
                <div class="input-div">
                    <div class="i">
                        <i class="fas fa-signature"></i>
                    </div>
                    <div>
                        <h5>Signature</h5>
                        <input type="text" id="signature" name="signature" required>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn">Soumettre</button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('submissionForm');
            form.addEventListener('submit', function(event) {
                event.preventDefault(); // Prevent page reload

                const formData = new FormData(form);

                fetch('submit.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        toastr.success(data.message);
                        form.reset(); // Reset form
                        setTimeout(() => {
                            window.location.href = 'dashboard.php'; // Redirect to dashboard after 2 seconds
                        });
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
    </script>
</body>
</html>

