<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_name = isset($_POST['user_name']) ? htmlspecialchars($_POST['user_name']) : '';
    $title = htmlspecialchars($_POST['title']);
    $description = htmlspecialchars($_POST['description']);

    // Valider les champs requis
    if (empty($title) || empty($description)) {
        $error_message = "Le titre et la description sont obligatoires.";
    } else {
        // Adresse email du cinéma
        $to = "contact@cinephoria.com";
        $subject = "Nouvelle demande de contact: $title";
        $message = "Nom de l'utilisateur: $user_name\n\nDescription:\n$description";
        $headers = "From: no-reply@cinephoria.com";

        // Envoyer l'email
        if (mail($to, $subject, $message, $headers)) {
            $success_message = "Votre demande a été envoyée avec succès.";
        } else {
            $error_message = "Une erreur s'est produite lors de l'envoi de votre demande. Veuillez réessayer.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <main>
        <h1>Contactez-nous</h1>
        <?php
        if (isset($error_message)) {
            echo "<p style='color:red;'>$error_message</p>";
        } elseif (isset($success_message)) {
            echo "<p style='color:green;'>$success_message</p>";
        }
        ?>
        <form method="POST" action="contact.php">
            <div>
                <label for="user_name">Nom d'utilisateur (facultatif):</label>
                <input type="text" id="user_name" name="user_name">
            </div>
            <div>
                <label for="title">Titre de la demande:</label>
                <input type="text" id="title" name="title" required>
            </div>
            <div>
                <label for="description">Description:</label>
                <textarea id="description" name="description" required></textarea>
            </div>
            <div>
                <button type="submit">Envoyer</button>
            </div>
        </form>
    </main>
    <?php include 'footer.php'; ?>
</body>
</html>
