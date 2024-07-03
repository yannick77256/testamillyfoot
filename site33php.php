<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    // Enregistrer les fichiers
    $uploads_dir = 'uploads/';
    $cv = $uploads_dir . basename($_FILES['cv']['name']);
    $cover_letter = $uploads_dir . basename($_FILES['cover_letter']['name']);

    // Vérifiez que le dossier uploads existe, sinon le créer
    if (!is_dir($uploads_dir)) {
        mkdir($uploads_dir, 0777, true);
    }

    move_uploaded_file($_FILES['cv']['tmp_name'], $cv);
    move_uploaded_file($_FILES['cover_letter']['tmp_name'], $cover_letter);

    // Envoyer l'e-mail avec les pièces jointes
    $to = 'tangyannick77@gmail.com'; // Remplacez par votre adresse e-mail
    $headers = "From: $email\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/mixed; boundary=\"_1_$boundary\"";

    $message_content = "
--_1_$boundary
Content-Type: multipart/alternative; boundary=\"_2_$boundary\"

--_2_$boundary
Content-Type: text/plain; charset=\"iso-8859-1\"
Content-Transfer-Encoding: 7bit

Nom: $name
Email: $email
Objet: $subject
Message: $message

--_2_$boundary--
--_1_$boundary
Content-Type: application/octet-stream; name=\"" . basename($cv) . "\"
Content-Transfer-Encoding: base64
Content-Disposition: attachment

" . chunk_split(base64_encode(file_get_contents($cv))) . "
--_1_$boundary
Content-Type: application/octet-stream; name=\"" . basename($cover_letter) . "\"
Content-Transfer-Encoding: base64
Content-Disposition: attachment

" . chunk_split(base64_encode(file_get_contents($cover_letter))) . "
--_1_$boundary--";

    if (mail($to, $subject, $message_content, $headers)) {
        echo 'Email envoyé avec succès!';
    } else {
        echo 'Échec de l\'envoi de l\'email...';
    }
}
?>
