<?php
// Inclure le fichier de connexion à la base de données
include 'db_connection.php';

// Démarrer la session
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Vérifier si une séance a été sélectionnée
if (isset($_POST['seance_id'])) {
    $seance_id = intval($_POST['seance_id']);
    
    // Préparer et exécuter la requête pour obtenir les détails de la séance
    $seance_sql = "SELECT s.h_debut, s.h_fin, s.num_salle, s.qualite, s.places_disponibles, s.prix, f.titre AS film_titre, c.nom AS cinema_nom
                   FROM seance s
                   JOIN film f ON s.film_id = f.id
                   JOIN cinema c ON f.cinema_id = c.id
                   WHERE s.id = ?";
    if ($stmt = $conn->prepare($seance_sql)) {
        $stmt->bind_param("i", $seance_id);
        $stmt->execute();
        $seance_result = $stmt->get_result();

        if ($seance_result->num_rows > 0) {
            $seance_row = $seance_result->fetch_assoc();
            $film_titre = htmlspecialchars($seance_row['film_titre'], ENT_QUOTES, 'UTF-8');
            $cinema_nom = htmlspecialchars($seance_row['cinema_nom'], ENT_QUOTES, 'UTF-8');
            $h_debut = htmlspecialchars(date("H:i", strtotime($seance_row['h_debut'])), ENT_QUOTES, 'UTF-8');
            $h_fin = htmlspecialchars(date("H:i", strtotime($seance_row['h_fin'])), ENT_QUOTES, 'UTF-8');
            $num_salle = htmlspecialchars($seance_row['num_salle'], ENT_QUOTES, 'UTF-8');
            $qualite = htmlspecialchars($seance_row['qualite'], ENT_QUOTES, 'UTF-8');
            $places_disponibles = intval($seance_row['places_disponibles']);
            $prix = floatval($seance_row['prix']);
        } else {
            echo "Aucune séance trouvée.";
            exit();
        }
        $stmt->close();
    } else {
        echo "Erreur de préparation de la requête : " . $conn->error;
        exit();
    }
} else {
    echo "Aucune séance sélectionnée.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réservation pour <?php echo $film_titre; ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <main>
        <h1>Réservation pour <?php echo $film_titre; ?></h1>
        <p><strong>Cinéma:</strong> <?php echo $cinema_nom; ?></p>
        <p><strong>Salle:</strong> <?php echo $num_salle; ?></p>
        <p><strong>Qualité:</strong> <?php echo $qualite; ?></p>
        <p><strong>Heure de début:</strong> <?php echo $h_debut; ?></p>
        <p><strong>Heure de fin:</strong> <?php echo $h_fin; ?></p>
        <p><strong>Places disponibles:</strong> <?php echo $places_disponibles; ?></p>
        <p><strong>Prix par place:</strong> <?php echo $prix; ?> €</p>

        <form method="POST" action="confirm_reservation.php">
            <input type="hidden" name="seance_id" value="<?php echo $seance_id; ?>">
            <label for="num_places">Nombre de places à réserver:</label>
            <input type="number" id="num_places" name="num_places" min="1" max="<?php echo $places_disponibles; ?>" required>
            <button type="submit">Confirmer la réservation</button>
        </form>
    </main>
    <?php include 'footer.php'; ?>
</body>
</html>

<?php
$conn->close();
?>
