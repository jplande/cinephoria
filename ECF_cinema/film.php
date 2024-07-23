<?php
// Inclure le fichier de connexion à la base de données
include 'db_connection.php';

// Assurez-vous que la connexion utilise UTF-8
$conn->set_charset("utf8");

// Démarrer la session
session_start();

// Préparer et exécuter la requête pour obtenir les films
$sql = "SELECT id, affiche_film, titre FROM film";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Afficher les films
    echo '<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Films</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>';

    include 'header.php';

    echo '<main>
    <h1>Liste des Films</h1>
    <div class="film-container">';

    // Parcourir les résultats et afficher chaque film
    while ($row = $result->fetch_assoc()) {
        $id = $row['id'];
        $affiche_film = $row['affiche_film'];
        $titre = $row['titre'];

        echo '<div class="film-item">
            <a href="film_details.php?film_id=' . $id . '">
                <img src="images/' . $affiche_film . '" alt="' . htmlspecialchars($titre, ENT_QUOTES, 'UTF-8') . '">
                <h2>' . htmlspecialchars($titre, ENT_QUOTES, 'UTF-8') . '</h2>
            </a>
        </div>';
    }

    echo '</div></main>';

    include 'footer.php';

    echo '</body>
</html>';
} else {
    echo "Aucun film trouvé.";
}

// Fermer la connexion
$conn->close();
?>
