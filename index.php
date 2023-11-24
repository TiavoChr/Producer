<?php
require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

header("Content-Type: application/json");

// Connexion à la base de données (à remplacer par vos informations)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "producer";

$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['email']) && isset($_POST['login']) && isset($_POST['mot_de_passe'])) {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $login = $_POST['login'];
    $mot_de_passe = $_POST['mot_de_passe'];

    // Vérifier si l'e-mail ou le login existe déjà en base de données
    $checkUserQuery = "SELECT id FROM utilisateur WHERE email = '$email' OR login = '$login'";
    $result = $conn->query($checkUserQuery);

    if ($result->num_rows > 0) {
        echo json_encode(["error" => "L'utilisateur avec cet e-mail ou ce login existe déjà"]);
    } else {
        // Insertion des données dans la base de données
        $sql = "INSERT INTO utilisateur (nom, prenom, email, login, mot_de_passe) VALUES ('$nom', '$prenom', '$email', '$login', '$mot_de_passe')";

        if ($conn->query($sql) === TRUE) {
            // Publier un message sur RabbitMQ
            $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
            $channel = $connection->channel();

            $channel->queue_declare('confirmation_email_queue', false, true, false, false);

            $messageData = [
                'email' => $email,
                'subject' => 'Confirmation d\'inscription',
                'message' => 'Merci de vous être inscrit. Veuillez confirmer votre compte.'
            ];

            $message = new AMQPMessage(json_encode($messageData));
            $channel->basic_publish($message, '', 'confirmation_email_queue');

            echo json_encode(["message" => "Utilisateur inscrit avec succès. Un e-mail de confirmation a été envoyé."]);
        } else {
            echo json_encode(["error" => "Erreur lors de l'inscription de l'utilisateur"]);
        }
    }
} else {
    echo json_encode(["error" => "Méthode non autorisée ou données manquantes"]);
}

// Fermer la connexion
$conn->close();
?>