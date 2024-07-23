<?php
include('db_connection.php'); // Connexion à la base de données

// Debugging function
function debug($message) {
    echo "<pre>$message</pre>";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ajouter un film
    if (isset($_POST['ajouter'])) {
        $affiche_film = $_POST['affiche_film'];
        $titre = $_POST['titre'];
        $description = $_POST['description'];
        $age_min = $_POST['age_min'];
        $label = $_POST['label'];
        $genre = $_POST['genre'];
        $cinema_id = $_POST['cinema_id'];
        $jour = $_POST['jour'];

        // Debugging the values before insertion
        debug("Valeurs reçues pour ajout :");
        debug("affiche_film: $affiche_film, titre: $titre, description: $description, age_min: $age_min, label: $label, genre: $genre, cinema_id: $cinema_id, jour: $jour");

        $sql = "INSERT INTO film (affiche_film, titre, description, age_min, label, genre, cinema_id, jour) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("sssissss", $affiche_film, $titre, $description, $age_min, $label, $genre, $cinema_id, $jour);
            if ($stmt->execute()) {
                debug("Film ajouté avec succès");
                header('Location: admin.php?page=films');
                exit();
            } else {
                debug("Erreur lors de l'ajout du film : " . $stmt->error);
            }
        } else {
            debug("Erreur lors de la préparation de la requête : " . $conn->error);
        }
    }
}

// Supprimer un film
if (isset($_GET['delete_film'])) {
    $id = $_GET['delete_film'];

    debug("Tentative de suppression du film avec ID: $id");

    $query = "DELETE FROM film WHERE id = ?";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            debug("Film supprimé avec succès");
            header('Location: admin.php?page=films');
            exit();
        } else {
            debug("Erreur lors de la suppression du film : " . $stmt->error);
        }
    } else {
        debug("Erreur lors de la préparation de la requête : " . $conn->error);
    }
}

$films = $conn->query("SELECT * FROM film");
if (!$films) {
    debug("Erreur lors de la récupération des films : " . $conn->error);
}
?>

<h2>Gérer les films</h2>
<form method="post" action="admin.php?page=films">
    <input type="text" name="affiche_film" placeholder="Affiche du film" required>
    <input type="text" name="titre" placeholder="Titre" required>
    <textarea name="description" placeholder="Description" required></textarea>
    <input type="number" name="age_min" placeholder="Âge minimum" required>
    <input type="text" name="label" placeholder="Label" required>
    <input type="text" name="genre" placeholder="Genre" required>
    <input type="number" name="cinema_id" placeholder="ID du cinéma" required>
    <input type="date" name="jour" placeholder="Jour" required>
    <button type="submit" name="ajouter">Ajouter un film</button>
</form>

<h3>Films existants</h3>
<table>
    <thead>
        <tr>
            <th>Affiche</th>
            <th>Titre</th>
            <th>Description</th>
            <th>Âge minimum</th>
            <th>Label</th>
            <th>Genre</th>
            <th>ID du cinéma</th>
            <th>Jour</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($film = $films->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $film['affiche_film']; ?></td>
                <td><?php echo $film['titre']; ?></td>
                <td><?php echo $film['description']; ?></td>
                <td><?php echo $film['age_min']; ?></td>
                <td><?php echo $film['label']; ?></td>
                <td><?php echo $film['genre']; ?></td>
                <td><?php echo $film['cinema_id']; ?></td>
                <td><?php echo $film['jour']; ?></td>
                <td>
                    <form method="get" action="admin.php" style="display:inline;">
                        <input type="hidden" name="page" value="films">
                        <input type="hidden" name="delete_film" value="<?php echo $film['id']; ?>">
                        <button type="submit">Supprimer</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>
