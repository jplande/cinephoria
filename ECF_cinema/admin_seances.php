<?php
include('db_connection.php'); // Connexion à la base de données

// Debugging function
function debug($message) {
    echo "<pre>$message</pre>";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ajouter une séance
    if (isset($_POST['ajouter'])) {
        $h_debut = $_POST['h_debut'];
        $h_fin = $_POST['h_fin'];
        $num_salle = $_POST['num_salle'];
        $film_id = $_POST['film_id'];
        $qualite = $_POST['qualite'];
        $places_disponibles = $_POST['places_disponibles'];
        $prix = $_POST['prix'];

        // Format the datetime values to ensure proper format
        $h_debut_formatted = date('Y-m-d H:i:s', strtotime($h_debut));
        $h_fin_formatted = date('Y-m-d H:i:s', strtotime($h_fin));

        // Debugging the values before insertion
        debug("Valeurs reçues pour ajout :");
        debug("h_debut: $h_debut_formatted, h_fin: $h_fin_formatted, num_salle: $num_salle, film_id: $film_id, qualite: $qualite, places_disponibles: $places_disponibles, prix: $prix");

        $sql = "INSERT INTO seance (h_debut, h_fin, num_salle, film_id, qualite, places_disponibles, prix) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("ssiisii", $h_debut_formatted, $h_fin_formatted, $num_salle, $film_id, $qualite, $places_disponibles, $prix);
            if ($stmt->execute()) {
                debug("Séance ajoutée avec succès");
                header('Location: admin.php?page=seances');
                exit();
            } else {
                debug("Erreur lors de l'ajout de la séance : " . $stmt->error);
            }
        } else {
            debug("Erreur lors de la préparation de la requête : " . $conn->error);
        }
    }
}

// Supprimer une séance
if (isset($_GET['delete_seance'])) {
    $id = $_GET['delete_seance'];

    debug("Tentative de suppression de la séance avec ID: $id");

    $query = "DELETE FROM seance WHERE id = ?";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            debug("Séance supprimée avec succès");
            header('Location: admin.php?page=seances');
            exit();
        } else {
            debug("Erreur lors de la suppression de la séance : " . $stmt->error);
        }
    } else {
        debug("Erreur lors de la préparation de la requête : " . $conn->error);
    }
}

$seances = $conn->query("SELECT * FROM seance");
if (!$seances) {
    debug("Erreur lors de la récupération des séances : " . $conn->error);
}
?>

<h2>Gérer les séances</h2>
<form method="post" action="admin.php?page=seances">
    <input type="datetime-local" name="h_debut" placeholder="Heure de début" required>
    <input type="datetime-local" name="h_fin" placeholder="Heure de fin" required>
    <input type="number" name="num_salle" placeholder="Numéro de salle" required>
    <input type="number" name="film_id" placeholder="ID du film" required>
    <input type="text" name="qualite" placeholder="Qualité" required>
    <input type="number" name="places_disponibles" placeholder="Places disponibles" required>
    <input type="number" name="prix" placeholder="Prix" required>
    <button type="submit" name="ajouter">Ajouter une séance</button>
</form>

<h3>Séances existantes</h3>
<table>
    <thead>
        <tr>
            <th>Heure de début</th>
            <th>Heure de fin</th>
            <th>Numéro de salle</th>
            <th>ID du film</th>
            <th>Qualité</th>
            <th>Places disponibles</th>
            <th>Prix</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($seance = $seances->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $seance['h_debut']; ?></td>
                <td><?php echo $seance['h_fin']; ?></td>
                <td><?php echo $seance['num_salle']; ?></td>
                <td><?php echo $seance['film_id']; ?></td>
                <td><?php echo $seance['qualite']; ?></td>
                <td><?php echo $seance['places_disponibles']; ?></td>
                <td><?php echo $seance['prix']; ?></td>
                <td>

                    <form method="get" action="admin.php" style="display:inline;">
                        <input type="hidden" name="page" value="seances">
                        <input type="hidden" name="delete_seance" value="<?php echo $seance['id']; ?>">
                        <button type="submit">Supprimer</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>
