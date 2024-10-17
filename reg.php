<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('db_connect.php'); // Database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Clean inputs
    $prenom = trim($_POST['prenom']);
    $nom = trim($_POST['nom']);
    $username = trim($_POST['username']);
    $password = password_hash(trim($_POST['password']), PASSWORD_BCRYPT);
    $email = strtolower($nom.'.'.$prenom.'@uam.edu.sn'); // Format email in lowercase

    // Check if the username already exists
    $checkQuery = "SELECT id FROM utilisateur2 WHERE username = ?";
    $stmt = $conn->prepare($checkQuery);
    if ($stmt === false) {
        $error = "Preparation error: " . htmlspecialchars($conn->error);
    } else {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Username is already taken. Please choose another.";
        } else {
            // Insert user
            $insertQuery = "INSERT INTO utilisateur2 (username, email, password, prenom, nom) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insertQuery);
            if ($stmt === false) {
                $error = "Preparation error: " . htmlspecialchars($conn->error);
            } else {
                $stmt->bind_param("sssss", $username, $email, $password, $prenom, $nom);
                
                if ($stmt->execute()) {
                    $_SESSION['user_id'] = $conn->insert_id;
                    echo "<script>alert('Account created successfully!'); window.location.href = 'dashboard.php';</script>";
                    exit();
                } else {
                    $error = "Execution error: " . htmlspecialchars($stmt->error);
                }
            }
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Create Account</title>
    <link rel="stylesheet" href="log.css">
    <link href="https://fonts.googleapis.com/css?family=Poppins:600&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a81368914c.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
    <img class="wave" src="wave3.png">
    <div class="container">
        <div class="img">
            <img src="bg.svg">
        </div>
        <div class="login-content">
            <form action="" method="post">
                <img src="avatar.svg">
                <h2 class="title">créer un compte</h2>
                
                <!-- Display errors -->
                <?php if (isset($error)) echo "<div class='error-message'>$error</div>"; ?>
                
                <div class="input-div one">
                    <div class="i">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="div">
                        <h5>Prénom</h5>
                        <input type="text" name="prenom" class="input" required>
                    </div>
                </div>
                
                <div class="input-div one">
                    <div class="i">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="div">
                        <h5>Nom</h5>
                        <input type="text" name="nom" class="input" required>
                    </div>
                </div>
                
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
                
                <input type="submit" class="btn" value="S'inscrire">
                <p>Déjà un compte ? <a href="log.php">Connectez-vous ici</a></p>
            </form>
        </div>
    </div>
    <script src="log.js"></script>
</body>
</html>
