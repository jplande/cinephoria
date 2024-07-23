<?php
// Inclure le fichier de connexion à la base de données
include 'db_connection.php';

// Assurez-vous que la connexion utilise UTF-8
$conn->set_charset("utf8");

// Démarrer la session
session_start();

// Récupérer la liste des cinémas
$cinemas_sql = "SELECT id, nom FROM cinema";
$cinemas_result = $conn->query($cinemas_sql);

// Récupérer la liste des films si un cinéma est sélectionné
$films_result = null;
if (isset($_GET['cinema_id'])) {
    $cinema_id = intval($_GET['cinema_id']);
    $films_sql = "SELECT id, titre FROM film WHERE cinema_id = ?";
    $stmt = $conn->prepare($films_sql);
    $stmt->bind_param("i", $cinema_id);
    $stmt->execute();
    $films_result = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réservation</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <main>
        <h1>Réserver une séance</h1>
        <form method="GET" action="reservation.php">
            <label for="cinema">Sélectionner un cinéma:</label>
            <select name="cinema_id" id="cinema" required>
                <option value="">--Choisir un cinéma--</option>
                <?php
                if ($cinemas_result->num_rows > 0) {
                    while ($row = $cinemas_result->fetch_assoc()) {
                        echo '<option value="' . $row['id'] . '">' . htmlspecialchars($row['nom']) . '</option>';
                    }
                }
                ?>
            </select>
            <button type="submit">Suivant</button>
        </form>

        <?php if ($films_result): ?>
        <form method="GET" action="seances.php">
            <input type="hidden" name="cinema_id" value="<?php echo $cinema_id; ?>">
            <label for="film">Sélectionner un film:</label>
            <select name="film_id" id="film" required>
                <option value="">--Choisir un film--</option>
                <?php
                if ($films_result->num_rows > 0) {
                    while ($row = $films_result->fetch_assoc()) {
                        echo '<option value="' . $row['id'] . '">' . htmlspecialchars($row['titre']) . '</option>';
                    }
                }
                ?>
            </select>
            <button type="submit">Voir les séances</button>
        </form>
        <?php endif; ?>
    </main>
    <?php include 'footer.php'; ?>
</body>
</html>

<?php
$conn->close();
?>
