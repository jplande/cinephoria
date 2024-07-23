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

// Vérifier si les données nécessaires ont été envoyées
if (isset($_POST['seance_id']) && isset($_POST['num_places'])) {
    $seance_id = intval($_POST['seance_id']);
    $num_places = intval($_POST['num_places']);
    $user_id = $_SESSION['user_id'];

    // Préparer et exécuter la requête pour insérer la réservation
    $reservation_sql = "INSERT INTO reservation (user_id, seance_id, num_places, prix_total) 
                        SELECT ?, ?, ?, ? * s.prix
                        FROM seance s
                        WHERE s.id = ?";
    if ($stmt = $conn->prepare($reservation_sql)) {
        $stmt->bind_param("iiiii", $user_id, $seance_id, $num_places, $num_places, $seance_id);
        if ($stmt->execute()) {
            echo "<script>
                    alert('La réservation a été confirmée avec succès!');
                    window.location.href = 'mon_espace.php';
                  </script>";
        } else {
            echo "<script>
                    alert('Erreur lors de la réservation. Veuillez réessayer.');
                    window.location.href = 'reservation_details.php?seance_id=$seance_id';
                  </script>";
        }
        $stmt->close();
    } else {
        echo "<script>
                alert('Erreur de préparation de la requête. Veuillez réessayer.');
                window.location.href = 'reservation_details.php?seance_id=$seance_id';
              </script>";
    }
} else {
    echo "<script>
            alert('Données manquantes. Veuillez réessayer.');
            window.location.href = 'reservation_details.php';
          </script>";
}

$conn->close();
?>
