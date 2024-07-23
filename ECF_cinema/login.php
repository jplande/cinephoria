<?php
session_start(); // Assurez-vous que session_start() est appelé

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php include 'header.php'; ?>

    <main>
        <h1>Connexion</h1>

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

                $stmt = $conn->prepare("SELECT id, pseudo, mdp FROM user WHERE pseudo = ?");
                $stmt->bind_param("s", $pseudo);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows === 1) {
                    $stmt->bind_result($user_id, $stored_pseudo, $stored_password);
                    $stmt->fetch();

                    if (password_verify($password, $stored_password)) {
                        $_SESSION['user_id'] = $user_id;
                        $_SESSION['pseudo'] = $stored_pseudo;
                        header("Location: index.php");
                        exit();
                    } else {
                        $errors[] = "Pseudo ou mot de passe incorrect.";
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

        <form method="post" action="login.php">
            <div>
                <label for="pseudo">Pseudo d'utilisateur:</label>
                <input type="text" id="pseudo" name="pseudo">
            </div>
            <div>
                <label for="password">Mot de passe:</label>
                <input type="password" id="password" name="password">
            </div>
            <div>
                <button type="submit">Se connecter</button>
            </div>
        </form>
        
        <p>Pas encore inscrit ? <a href="inscription.php">Inscrivez-vous ici</a></p>
    </main>

    <?php include 'footer.php'; ?>
</body>

</html>
