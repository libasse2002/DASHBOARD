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
    <div class="container">
        <h2 class="title">Nouvelle Soumission</h2>
        <form id="submissionForm" action="submit.php" method="POST">
            <input type="hidden" name="submit" value="1">

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
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div>
                    <h5>Filière</h5>
                    <select id="filiere" name="filiere" required>
                        <option value="">Sélectionnez une filière</option>
                    </select>
                </div>
            </div>

            <div class="input-div">
                <div class="i">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div>
                    <h5>Niveau</h5>
                    <select id="niveau" name="niveau" required>
                        <option value="">Sélectionnez un niveau</option>
                    </select>
                </div>
            </div>

            <div class="input-div">
                <div class="i">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div>
                    <h5>Semestre</h5>
                    <select id="semestre" name="semestre" required>
                        <option value="">Sélectionnez un semestre</option>
                    </select>
                </div>
            </div>

            <div class="input-div">
                <div class="i">
                    <i class="fas fa-book"></i>
                </div>
                <div>
                    <h5>Nom EC</h5>
                    <select id="ec" name="ec" required>
                        <option value="">Sélectionnez un EC</option>
                    </select>
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

    <script>
        document.getElementById('department').addEventListener('change', function() {
            const departmentId = this.value;
            fetch('get_filieres.php?departement_id=' + departmentId)
                .then(response => response.json())
                .then(data => {
                    const filiereSelect = document.getElementById('filiere');
                    filiereSelect.innerHTML = '<option value="">Sélectionnez une filière</option>';
                    data.forEach(filiere => {
                        filiereSelect.innerHTML += `<option value="${filiere.id}">${filiere.nom}</option>`;
                    });
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur lors de la récupération des filières. Veuillez réessayer.');
                });
        });

        document.getElementById('filiere').addEventListener('change', function() {
            const filiereId = this.value;
            fetch('get_niveaux.php?filiere_id=' + filiereId)
                .then(response => response.json())
                .then(data => {
                    const niveauSelect = document.getElementById('niveau');
                    niveauSelect.innerHTML = '<option value="">Sélectionnez un niveau</option>';
                    data.forEach(niveau => {
                        niveauSelect.innerHTML += `<option value="${niveau.id}">${niveau.nom}</option>`;
                    });
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur lors de la récupération des niveaux. Veuillez réessayer.');
                });
        });

        document.getElementById('niveau').addEventListener('change', function() {
            const niveauId = this.value;
            fetch('get_semestres.php?niveau_id=' + niveauId)
                .then(response => response.json())
                .then(data => {
                    const semestreSelect = document.getElementById('semestre');
                    semestreSelect.innerHTML = '<option value="">Sélectionnez un semestre</option>';
                    data.forEach(semestre => {
                        semestreSelect.innerHTML += `<option value="${semestre.id}">${semestre.nom}</option>`;
                    });
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur lors de la récupération des semestres. Veuillez réessayer.');
                });
        });

        document.getElementById('semestre').addEventListener('change', function() {
            const semestreId = this.value;
            const niveauId = document.getElementById('niveau').value;
            const filiereId = document.getElementById('filiere').value;

            fetch('get_ec.php?semestre_id=' + semestreId + '&niveau_id=' + niveauId + '&filiere_id=' + filiereId)
                .then(response => response.json())
                .then(data => {
                    const ecSelect = document.getElementById('ec');
                    ecSelect.innerHTML = '<option value="">Sélectionnez un EC</option>';
                    if (data.error) {
                        console.error(data.error);
                    } else {
                        data.forEach(ec => {
                            ecSelect.innerHTML += `<option value="${ec.id}">${ec.nom_ec}</option>`;
                        });
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur lors de la récupération des EC. Veuillez réessayer.');
                });
        });
    </script>
</body>
</html>
