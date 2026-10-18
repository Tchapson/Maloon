<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Activer les erreurs pour debug
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);



require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {


    // Anti-spam : champ caché
    if (!empty($_POST['website'])) {
        exit('Spam détecté');
    }

    // Nettoyage des données
    $nom = htmlspecialchars(trim(is_string($_POST['nom'] ?? null) ? $_POST['nom'] : ''));
    $prenom = htmlspecialchars(trim(is_string($_POST['prenom'] ?? null) ? $_POST['prenom'] : ''));
    $email = filter_var(trim(is_string($_POST['email'] ?? null) ? $_POST['email'] : ''), FILTER_VALIDATE_EMAIL);
    $telephone = htmlspecialchars(trim(is_string($_POST['telephone'] ?? null) ? $_POST['telephone'] : ''));
    $date = htmlspecialchars(trim(is_string($_POST['date'] ?? null) ? $_POST['date'] : ''));
    $heure = htmlspecialchars(trim(is_string($_POST['heure'] ?? null) ? $_POST['heure'] : ''));
    $motif = htmlspecialchars(trim(is_string($_POST['motif'] ?? null) ? $_POST['motif'] : ''));

    // Vérification des champs obligatoires
    if (empty($nom) || empty($prenom) || !$email || empty($date) || empty($heure) || empty($motif)) {
        http_response_code(400);
        echo "Veuillez remplir tous les champs obligatoires correctement.";
        exit;
    }

    if (!getenv('SMTP_USERNAME') || !getenv('SMTP_PASSWORD') || !getenv('MAIL_TO')) {
        http_response_code(503);
        exit("Le service de rendez-vous est temporairement indisponible.");
    }

    // Configuration de PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Configuration SMTP Gmail
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username = getenv('SMTP_USERNAME') ?: ''; // 👉 Remplace par TON adresse Gmail
        $mail->Password = getenv('SMTP_PASSWORD') ?: ''; // 👉 Ton mot de passe d’application Gmail (pas ton mot de passe normal)
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Paramètres de l'expéditeur et destinataire
        $mail->setFrom($mail->Username, 'Maloon'); // envoyé depuis ton mail
        $mail->addAddress(getenv('MAIL_TO') ?: '');

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
        header("Location: ../Merci.html", true, 303);
        exit;

    } catch (Exception $e) {
        http_response_code(502);
        echo "L’envoi a échoué. Veuillez réessayer ultérieurement.";
    }

} else {
    http_response_code(403);
    echo "Méthode non autorisée.";
}
?>






