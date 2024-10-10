<?php
session_start();
include('db_connect.php'); // Assurez-vous que le chemin est correct

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    // Récupérer l'utilisateur de la base de données
    $query = "SELECT * FROM utilisateur2 WHERE username = '$username'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    // Vérifier le mot de passe
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id']; // Stocker l'ID de l'utilisateur dans la session
        header('Location: dashboard.php'); // Rediriger vers le tableau de bord
        exit();
    } else {
        $error = "Nom d'utilisateur ou mot de passe incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <div class="content">
        <h2>Connexion</h2>
        <?php if (isset($error)) echo "<p>$error</p>"; ?>
        <form action="" method="post">
            <label for="username">Nom d'utilisateur :</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Mot de passe :</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Se Connecter</button>
        </form>
        <p>Pas encore de compte ? <a href="register.php">Créez-en un ici</a></p>
    </div>
</body>
</html>
