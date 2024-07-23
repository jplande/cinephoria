<?php
// Inclure le fichier de connexion à la base de données
include 'db_connection.php';

// Assurez-vous que la connexion utilise UTF-8
$conn->set_charset("utf8");

// Démarrer la session
session_start();

// Vérifier si un ID de film a été passé en paramètre
if (isset($_GET['film_id'])) {
    $film_id = intval($_GET['film_id']);
    
    // Préparer et exécuter la requête pour obtenir les détails du film
    $film_sql = "SELECT f.affiche_film, f.titre, f.description, f.age_min, f.label, f.genre, c.nom AS cinema, f.jour 
                 FROM film f 
                 JOIN cinema c ON f.cinema_id = c.id 
                 WHERE f.id = ?";
    $stmt = $conn->prepare($film_sql);
    if (!$stmt) {
        echo "Échec de la préparation de la requête : (" . $conn->errno . ") " . $conn->error;
        exit();
    }
    $stmt->bind_param("i", $film_id);
    $stmt->execute();
    $film_result = $stmt->get_result();
    
    if ($film_result->num_rows > 0) {
        $film_row = $film_result->fetch_assoc();
        $film_title = htmlspecialchars($film_row['titre'], ENT_QUOTES, 'UTF-8');
        $film_description = htmlspecialchars($film_row['description'], ENT_QUOTES, 'UTF-8');
        $film_age_min = htmlspecialchars($film_row['age_min'], ENT_QUOTES, 'UTF-8');
        $film_label = htmlspecialchars($film_row['label'], ENT_QUOTES, 'UTF-8');
        $film_genre = htmlspecialchars($film_row['genre'], ENT_QUOTES, 'UTF-8');
        $film_cinema = htmlspecialchars($film_row['cinema'], ENT_QUOTES, 'UTF-8');
        $film_jour = htmlspecialchars($film_row['jour'], ENT_QUOTES, 'UTF-8');
        $affiche_film = htmlspecialchars($film_row['affiche_film'], ENT_QUOTES, 'UTF-8');

        // Préparer et exécuter la requête pour obtenir les réservations disponibles
        $reservation_sql = "SELECT h_debut, h_fin, num_salle, prix FROM seance WHERE film_id = ?";
        $stmt = $conn->prepare($reservation_sql);
        if (!$stmt) {
            echo "Échec de la préparation de la requête : (" . $conn->errno . ") " . $conn->error;
            exit();
        }
        $stmt->bind_param("i", $film_id);
        $stmt->execute();
        $reservation_result = $stmt->get_result();
        
        // Afficher les détails du film et les séances disponibles
        echo '<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du film ' . $film_title . '</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>';

        include 'header.php';

        echo '<main>
        <div class="film-details">
            <img src="images/' . $affiche_film . '" alt="' . $film_title . '">
            <h1>' . $film_title . '</h1>
            <p><strong>Description:</strong> ' . $film_description . '</p>
            <p><strong>Âge minimum:</strong> ' . $film_age_min . ' ans</p>
            <p><strong>Label:</strong> ' . $film_label . '</p>
            <p><strong>Genre:</strong> ' . $film_genre . '</p>
            <p><strong>Cinéma:</strong> ' . $film_cinema . '</p>
            <p><strong>Jour:</strong> ' . $film_jour . '</p>
        </div>';

        if ($reservation_result->num_rows > 0) {
            echo '<h2>Réservations disponibles</h2>
            <table>
                <thead>
                    <tr>
                        <th>Heure de début</th>
                        <th>Heure de fin</th>
                        <th>Numéro de salle</th>
                        <th>Prix</th>
                        <th>Réserver</th>
                    </tr>
                </thead>
                <tbody>';
                
            while ($row = $reservation_result->fetch_assoc()) {
                $h_debut = date("H:i", strtotime($row['h_debut']));
                $h_fin = date("H:i", strtotime($row['h_fin']));
                $num_salle = htmlspecialchars($row['num_salle'], ENT_QUOTES, 'UTF-8');
                $prix = htmlspecialchars($row['prix'], ENT_QUOTES, 'UTF-8');
                
                echo '<tr>
                    <td>' . $h_debut . '</td>
                    <td>' . $h_fin . '</td>
                    <td>' . $num_salle . '</td>
                    <td>' . $prix . ' €</td>';
                
                if (isset($_SESSION['user_id'])) {
                    echo '<td><a href="confirm_reservation.php?film_id=' . $film_id . '&h_debut=' . urlencode($h_debut) . '&num_salle=' . $num_salle . '">Réserver</a></td>';
                } else {
                    echo '<td><a href="login.php">Se connecter pour réserver</a></td>';
                }
                
                echo '</tr>';
            }
            
            echo '</tbody></table>';
        } else {
            echo '<p>Aucune séance disponible pour ce film.</p>';
        }

        echo '</main>';

        include 'footer.php';

        echo '</body>
</html>';
    } else {
        echo "Film non trouvé.";
    }
    
    // Fermer la déclaration préparée
    $stmt->close();
} else {
    echo "Aucun film sélectionné.";
}

// Fermer la connexion
$conn->close();
?>
