<?php
// Démarrer la session uniquement si elle n'est pas déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('db_connect.php'); // Connexion à la base de données

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupérer et échapper les données
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Préparer la requête
    $query = "SELECT id, password FROM utilisateur2 WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Vérifier si un utilisateur existe avec ce nom d'utilisateur
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Vérifier le mot de passe
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            header('Location: dashboard.php');
            exit();
        } else {
            $error = "Nom d'utilisateur ou mot de passe incorrect.";
        }
    } else {
        $error = "Nom d'utilisateur ou mot de passe incorrect.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <link rel="stylesheet" href="log.css">
    <link href="https://fonts.googleapis.com/css?family=Poppins:600&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a81368914c.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
    <img class="wave" src="wave3.png">
    <div class="container">
        <div class="img">
            <img src="undraw_Calculator_re_alsc.png">
        </div>
        <div class="login-content">
            <form action="" method="post">
                <img src="undraw_Pic_profile_re_7g2h.png">
                <h2 class="title">Bienvenue</h2>
                
                <!-- Affichage des erreurs -->
                <?php if (isset($error)) echo "<div class='error-message'>$error</div>"; ?>
                
                <div class="input-div one">
                    <div class="i">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="div">
                        <h5>Nom d'utilisateur</h5>
                        <input type="text" name="username" class="input" required>
                    </div>
                </div>
                
                <div class="input-div pass">
                    <div class="i"> 
                        <i class="fas fa-lock"></i>
                    </div>
                    <div class="div">
                        <h5>Mot de passe</h5>
                        <input type="password" name="password" class="input" required>
                    </div>
                </div>
                
                <a href="#">Mot de passe oublié ?</a>
                <input type="submit" class="btn" value="Connexion">
                
                <!-- Lien pour créer un compte -->
                <p>Pas encore de compte ? <a href="reg.php">Créez-en un ici</a></p>
            </form>
        </div>
    </div>
    <script src="log.js"></script>
</body>
</html>
