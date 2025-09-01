<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Activer les erreurs pour debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "Traitement.php appelé<br>";

require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    echo "Requête POST reçue<br>";

    // Anti-spam : champ caché
    if (!empty($_POST['website'])) {
        exit('Spam détecté');
    }

    // Nettoyage des données
    $nom = htmlspecialchars(trim($_POST['nom']));
    $prenom = htmlspecialchars(trim($_POST['prenom']));
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $telephone = htmlspecialchars(trim($_POST['telephone']));
    $date = htmlspecialchars(trim($_POST['date']));
    $heure = htmlspecialchars(trim($_POST['heure']));
    $motif = htmlspecialchars(trim($_POST['motif']));

    // Vérification des champs obligatoires
    if (empty($nom) || empty($prenom) || !$email || empty($date) || empty($heure) || empty($motif)) {
        http_response_code(400);
        echo "Veuillez remplir tous les champs obligatoires correctement.";
        exit;
    }

    // Affichage des données reçues pour debug
    echo nl2br("Données reçues :\n");
    echo "Nom : $nom\n";
    echo "Prénom : $prenom\n";
    echo "Email : $email\n";
    echo "Téléphone : $telephone\n";
    echo "Date souhaitée : $date\n";
    echo "Heure souhaitée : $heure\n";
    echo "Motif : $motif\n";

    // Configuration de PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Configuration SMTP Gmail
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'ymawt22@gmail.com'; // 👉 Remplace par TON adresse Gmail
        $mail->Password   = 'jpvu egqh ddht hzfn'; // 👉 Ton mot de passe d’application Gmail (pas ton mot de passe normal)
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Paramètres de l'expéditeur et destinataire
        $mail->setFrom($mail->Username, "$prenom $nom"); // envoyé depuis ton mail
        $mail->addAddress('ymawt22@gmail.com', 'Yaye');

        // Contenu du mail
        $mail->isHTML(false);
        $mail->Subject = 'Nouvelle demande de rendez-vous';
        $mail->Body    =
            "Vous avez reçu une nouvelle demande de rendez-vous.\n\n" .
            "Nom : $nom\n" .
            "Prénom : $prenom\n" .
            "Email : $email\n" .
            "Téléphone : $telephone\n" .
            "Date souhaitée : $date\n" .
            "Heure souhaitée : $heure\n" .
            "Motif : $motif\n";

        $mail->send();
        header("Location: ../Html/Merci.html");
        exit;

    } catch (Exception $e) {
        echo "Erreur lors de l'envoi du mail. Mailer Error : " . $mail->ErrorInfo;
    }

} else {
    http_response_code(403);
    echo "Méthode non autorisée.";
}
?>






