<?php
session_start(); // Assurez-vous que session_start() est appelé

if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'employe') {
    header("Location: admin.php");
    exit();
}

function debug($message) {
    echo "<pre>$message</pre>";
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Employé</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php include 'header.php'; ?>

    <main>
        <h1>Connexion Employé</h1>

        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $pseudo = htmlspecialchars($_POST['pseudo']);
            $password = $_POST['password']; // Note: this will be ignored in this implementation

            debug("Formulaire reçu :");
            debug("Pseudo : '$pseudo'");
            debug("Mot de passe : '$password'");

            $errors = [];

            if (empty($pseudo)) {
                $errors[] = "Le pseudo est requis.";
            }

            if (empty($errors)) {
                include 'db_connection.php';

                $stmt = $conn->prepare("SELECT id, pseudo, role FROM user WHERE pseudo = ?");
                $stmt->bind_param("s", $pseudo);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows === 1) {
                    $stmt->bind_result($user_id, $stored_pseudo, $role);
                    $stmt->fetch();

                    debug("Pseudo saisi: '$pseudo'");
                    debug("Pseudo stocké: '$stored_pseudo'");
                    debug("Rôle: '$role'");

                    if ($role === 'employe') {
                        $_SESSION['user_id'] = $user_id;
                        $_SESSION['pseudo'] = $stored_pseudo;
                        $_SESSION['role'] = $role;
                        header("Location: admin_employe.php");
                        exit();
                    } else {
                        $errors[] = "Vous n'avez pas les permissions nécessaires.";
                    }
                } else {
                    $errors[] = "Pseudo incorrect.";
                }

                $stmt->close();
                $conn->close();
            }

            foreach ($errors as $error) {
                echo "<p>$error</p>";
            }
        }
        ?>

        <form method="post" action="employe_login.php">
            <div>
                <label for="pseudo">Pseudo d'utilisateur:</label>
                <input type="text" id="pseudo" name="pseudo" required>
            </div>
            <div>
                <label for="password">Mot de passe:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div>
                <button type="submit">Se connecter</button>
            </div>
        </form>
        
        <p><a href="admin_login.php">Connexion Admin</a></p>
        <p><a href="login.php">Retour à la connexion utilisateur</a></p>
    </main>

    <?php include 'footer.php'; ?>
</body>

</html>
