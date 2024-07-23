<?php
session_start(); // Assurez-vous que session_start() est appelé

if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'employe')) {
    header("Location: admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Admin</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <main>
        <h1>Connexion Admin</h1>

        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $pseudo = htmlspecialchars($_POST['pseudo']);
            $password = htmlspecialchars($_POST['password']);

            $errors = [];

            if (empty($pseudo)) {
                $errors[] = "Le pseudo est requis.";
            }
            if (empty($password)) {
                $errors[] = "Le mot de passe est requis.";
            }

            if (empty($errors)) {
                include 'db_connection.php';

                $stmt = $conn->prepare("SELECT id, pseudo, mdp, role FROM user WHERE pseudo = ?");
                $stmt->bind_param("s", $pseudo);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows === 1) {
                    $stmt->bind_result($user_id, $stored_pseudo, $stored_password, $role);
                    $stmt->fetch();

                    if (password_verify($password, $stored_password) && $role === 'admin') {
                        $_SESSION['user_id'] = $user_id;
                        $_SESSION['pseudo'] = $stored_pseudo;
                        $_SESSION['role'] = $role;
                        header("Location: admin.php");
                        exit();
                    } else {
                        $errors[] = "Pseudo ou mot de passe incorrect ou vous n'avez pas les permissions nécessaires.";
                    }
                } else {
                    $errors[] = "Pseudo ou mot de passe incorrect.";
                }

                $stmt->close();
                $conn->close();
            }

            foreach ($errors as $error) {
                echo "<p>$error</p>";
            }
        }
        ?>

        <form method="post" action="admin_login.php">
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
        
        <p><a href="employe_login.php">Connexion Employé</a></p>
        <p><a href="login.php">Retour à la connexion utilisateur</a></p>
    </main>
</body>

</html>
