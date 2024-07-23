<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cinéma</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="bloc-principal">
        <?php include 'header.php'; ?>

        <main>
            <h1>Derniers films ajoutés un mercredi</h1>
            <?php
            // Connexion à la base de données
            include 'db_connection.php';

            // Récupérer le mercredi le plus récent où des films ont été ajoutés
            $stmt = $conn->prepare("SELECT MAX(jour) AS dernier_mercredi FROM film WHERE DAYOFWEEK(jour) = 4");
            if (!$stmt) {
                echo "Échec de la préparation de la requête : (" . $conn->errno . ") " . $conn->error;
            } else {
                $stmt->execute();
                $stmt->bind_result($dernier_mercredi);
                $stmt->fetch();
                $stmt->close();

                if ($dernier_mercredi) {
                    // Récupérer les films ajoutés ce mercredi
                    $stmt = $conn->prepare("SELECT f.id, f.affiche_film, f.titre, f.description, f.age_min, f.label, f.genre, c.nom AS cinema, f.jour 
                                            FROM film f 
                                            JOIN cinema c ON f.cinema_id = c.id 
                                            WHERE f.jour = ?");
                    if (!$stmt) {
                        echo "Échec de la préparation de la requête : (" . $conn->errno . ") " . $conn->error;
                    } else {
                        $stmt->bind_param("s", $dernier_mercredi);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows > 0) {
                            echo '<div class="film-container">';
                            while ($row = $result->fetch_assoc()) {
                                echo '<div class="film-item">';
                                echo '<a href="film_details.php?film_id=' . $row['id'] . '">';
                                echo '<img src="images/' . htmlspecialchars($row['affiche_film']) . '" alt="' . htmlspecialchars($row['titre']) . '">';
                                echo '<h2>' . htmlspecialchars($row['titre']) . '</h2>';
                                echo '</a>';
                                echo '<p>' . htmlspecialchars($row['description']) . '</p>';
                                echo '<p><strong>Âge minimum:</strong> ' . htmlspecialchars($row['age_min']) . ' ans</p>';
                                echo '<p><strong>Label:</strong> ' . htmlspecialchars($row['label']) . '</p>';
                                echo '<p><strong>Genre:</strong> ' . htmlspecialchars($row['genre']) . '</p>';
                                echo '<p><strong>Cinéma:</strong> ' . htmlspecialchars($row['cinema']) . '</p>';
                                echo '</div>';
                            }
                            echo '</div>';
                        } else {
                            echo '<p>Aucun film ajouté le dernier mercredi.</p>';
                        }

                        $stmt->close();
                    }
                } else {
                    echo '<p>Aucun film ajouté un mercredi récent.</p>';
                }
            }

            // Récupérer les 8 films les plus récemment ajoutés
            echo '<h1>8 films récemment ajoutés</h1>';
            $stmt = $conn->prepare("SELECT f.id, f.affiche_film, f.titre, f.description, f.age_min, f.label, f.genre, c.nom AS cinema, f.jour 
                                    FROM film f 
                                    JOIN cinema c ON f.cinema_id = c.id 
                                    ORDER BY f.jour DESC LIMIT 8");
            if (!$stmt) {
                echo "Échec de la préparation de la requête : (" . $conn->errno . ") " . $conn->error;
            } else {
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    echo '<div class="film-container">';
                    while ($row = $result->fetch_assoc()) {
                        echo '<div class="film-item">';
                        echo '<a href="film_details.php?film_id=' . $row['id'] . '">';
                        echo '<img src="images/' . htmlspecialchars($row['affiche_film']) . '" alt="' . htmlspecialchars($row['titre']) . '">';
                        echo '<h2>' . htmlspecialchars($row['titre']) . '</h2>';
                        echo '</a>';
                        echo '<p>' . htmlspecialchars($row['description']) . '</p>';
                        echo '<p><strong>Âge minimum:</strong> ' . htmlspecialchars($row['age_min']) . ' ans</p>';
                        echo '<p><strong>Label:</strong> ' . htmlspecialchars($row['label']) . '</p>';
                        echo '<p><strong>Genre:</strong> ' . htmlspecialchars($row['genre']) . '</p>';
                        echo '<p><strong>Cinéma:</strong> ' . htmlspecialchars($row['cinema']) . '</p>';
                        echo '</div>';
                    }
                    echo '</div>';
                } else {
                    echo '<p>Aucun film récemment ajouté.</p>';
                }

                $stmt->close();
            }
            $conn->close();
            ?>
        </main>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
