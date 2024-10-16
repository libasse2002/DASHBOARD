<?php
include 'db_connect.php';
session_start();

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: log.php'); // Redirige vers la page de connexion si non connecté
    exit();
}

$userId = $_SESSION['user_id'];

// Récupérer l'ID de la fiche à modifier
$ficheId = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($ficheId === 0) {
    header('Location: mes_fiches.php'); // Redirige si aucun ID n'est fourni
    exit();
}

// Récupérer les informations de la fiche
$query = "
    SELECT * FROM fiches 
    WHERE id = ? AND utilisateur_id = ? AND (statut = 'en_attente' OR statut = 'refusée')
";
$stmt = $conn->prepare($query);
$stmt->bind_param('ii', $ficheId, $userId);
$stmt->execute();
$fiche = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$fiche) {
    header('Location: mes_fiches.php'); // Redirige si la fiche n'est pas trouvée ou non modifiable
    exit();
}

// Récupérer la liste des départements avec leurs filières, niveaux, semestres et EC
$departmentsQuery = "SELECT * FROM departements";
$departmentsResult = $conn->query($departmentsQuery);
$departements = $departmentsResult->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Fiche</title>
    <link rel="stylesheet" href="subm.css">
    <script src="https://kit.fontawesome.com/a81368914c.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
    <div class="container">
        <h2 class="title">Modifier Fiche</h2>
        <form id="modifierForm" action="modifier_fiche_action.php" method="POST">
            <input type="hidden" name="fiche_id" value="<?= htmlspecialchars($fiche['id']); ?>">

            <!-- Sélection des départements -->
            <div class="input-div one">
                <div class="i"><i class="fas fa-building"></i></div>
                <div>
                    <h5>Département</h5>
                    <select id="department" name="department" required>
                        <?php foreach ($departements as $dept): ?>
                            <option value="<?= $dept['id']; ?>" <?= $dept['id'] == $fiche['departement_id'] ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($dept['nom']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Filieres, Niveaux, Semestres et EC -->
            <div class="input-div">
                <div class="i"><i class="fas fa-graduation-cap"></i></div>
                <div>
                    <h5>Filière</h5>
                    <select id="filiere" name="filiere" required>
                        <option value="">Sélectionnez une filière</option>
                    </select>
                </div>
            </div>

            <div class="input-div">
                <div class="i"><i class="fas fa-graduation-cap"></i></div>
                <div>
                    <h5>Niveau</h5>
                    <select id="niveau" name="niveau" required>
                        <option value="">Sélectionnez un niveau</option>
                    </select>
                </div>
            </div>

            <div class="input-div">
                <div class="i"><i class="fas fa-calendar-alt"></i></div>
                <div>
                    <h5>Semestre</h5>
                    <select id="semestre" name="semestre" required>
                        <option value="">Sélectionnez un semestre</option>
                    </select>
                </div>
            </div>

            <div class="input-div">
                <div class="i"><i class="fas fa-book"></i></div>
                <div>
                    <h5>Nom EC</h5>
                    <select id="ec" name="ec" required>
                        <option value="">Sélectionnez un EC</option>
                    </select>
                </div>
            </div>

            <!-- Champs pour les heures et la signature -->
            <div class="input-div"><div class="i"><i class="fas fa-clock"></i></div>
                <div><h5>Heures CM</h5><input type="number" name="hoursCM" value="<?= htmlspecialchars($fiche['hours_cm']); ?>" required></div>
            </div>
            <div class="input-div"><div class="i"><i class="fas fa-clock"></i></div>
                <div><h5>Heures TD</h5><input type="number" name="hoursTD" value="<?= htmlspecialchars($fiche['hours_td']); ?>" required></div>
            </div>
            <div class="input-div"><div class="i"><i class="fas fa-clock"></i></div>
                <div><h5>Heures TP</h5><input type="number" name="hoursTP" value="<?= htmlspecialchars($fiche['hours_tp']); ?>" required></div>
            </div>
            <div class="input-div"><div class="i"><i class="fas fa-signature"></i></div>
                <div><h5>Signature</h5><input type="text" name="signature" value="<?= htmlspecialchars($fiche['signature']); ?>" required></div>
            </div>

            <button type="submit" class="btn">Mettre à jour</button>
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

        // Initialisation des sélections avec les valeurs existantes
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('department').dispatchEvent(new Event('change'));
            document.getElementById('filiere').value = '<?= htmlspecialchars($fiche['filiere_id']); ?>';
            document.getElementById('niveau').value = '<?= htmlspecialchars($fiche['niveau_id']); ?>';
            document.getElementById('semestre').value = '<?= htmlspecialchars($fiche['semestre_id']); ?>';
            document.getElementById('ec').value = '<?= htmlspecialchars($fiche['ec_id']); ?>';
        });
    </script>
</body>
</html>
