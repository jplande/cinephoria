<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php include 'header.php'; ?>

    <main>
        <h1>Inscription</h1>

        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $nom = htmlspecialchars($_POST['nom']);
            $prenom = htmlspecialchars($_POST['prenom']);
            $pseudo = htmlspecialchars($_POST['pseudo']);
            $email = htmlspecialchars($_POST['email']);
            $password = htmlspecialchars($_POST['password']);
            $confirm_password = htmlspecialchars($_POST['confirm_password']);

            $errors = [];

            // Validation de base
            if (empty($nom)) {
                $errors[] = "Le nom est requis.";
            }
            if (empty($prenom)) {
                $errors[] = "Le prénom est requis.";
            }
            if (empty($pseudo)) {
                $errors[] = "Le pseudo est requis.";
            }
            if (empty($email)) {
                $errors[] = "L'email est requis.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Format d'email invalide.";
            }
            if (empty($password)) {
                $errors[] = "Le mot de passe est requis.";
            } elseif (strlen($password) < 6) {
                $errors[] = "Le mot de passe doit contenir au moins 6 caractères.";
            }
            if ($password !== $confirm_password) {
                $errors[] = "Les mots de passe ne correspondent pas.";
            }

            // Si aucune erreur, vous pouvez insérer les données dans la base de données
            if (empty($errors)) {
                // Inclure le fichier de connexion à la base de données
                include 'db_connection.php';

                // Sécuriser le mot de passe
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Préparer et exécuter la requête d'insertion
                $stmt = $conn->prepare("INSERT INTO user (nom, prenom, pseudo, mail, mdp) VALUES (?, ?, ?, ?, ?)");

                if ($stmt === false) {
                    die("Erreur de préparation de la requête : " . $conn->error);
                }

                $stmt->bind_param("sssss", $nom, $prenom, $pseudo, $email, $hashed_password);

                if ($stmt->execute()) {
                    echo "<p>Inscription réussie !</p>";
                } else {
                    echo "<p>Erreur lors de l'inscription : " . $stmt->error . "</p>";
                }

                $stmt->close();
                $conn->close();
            } else {
                // Afficher les erreurs
                foreach ($errors as $error) {
                    echo "<p>$error</p>";
                }
            }
        }
        ?>

        <form method="post" action="inscription.php">
            <div>
                <label for="nom">Nom:</label>
                <input type="text" id="nom" name="nom">
            </div>
            <div>
                <label for="prenom">Prénom:</label>
                <input type="text" id="prenom" name="prenom">
            </div>
            <div>
                <label for="pseudo">Pseudo:</label>
                <input type="text" id="pseudo" name="pseudo">
            </div>
            <div>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email">
            </div>
            <div>
                <label for="password">Mot de passe:</label>
                <input type="password" id="password" name="password">
            </div>
            <div>
                <label for="confirm_password">Confirmer le mot de passe:</label>
                <input type="password" id="confirm_password" name="confirm_password">
            </div>
            <div>
                <button type="submit">S'inscrire</button>
            </div>
        </form>
    </main>

    <?php include 'footer.php'; ?>
</body>

</html>