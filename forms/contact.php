<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php'; // caminho do Composer

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Método inválido']);
    exit;
}

$name    = $_POST['name'] ?? '';
$email   = $_POST['email'] ?? '';
$subject = $_POST['subject'] ?? '';
$message = $_POST['message'] ?? '';

$mail = new PHPMailer(true);


// Forçar UTF-8
$mail->CharSet = 'UTF-8';

try {
    // Configurações SMTP do cPanel
        $mail->isSMTP();
        $mail->Host       = 'troi.ptservidor.net';  // Substitua pelo seu servidor SMTP
        $mail->SMTPAuth   = true;
        $mail->Username   = 'geral@visioria.pt'; // Seu email
        $mail->Password   = 'Visioria@ptservidore05';               // Sua senha
        $mail->SMTPSecure = 'ssl';                    // ou 'ssl'
        $mail->Port       = 465;                      // Porta SMTP (587 TLS, 465 SSL)

    // Remetente e destinatário
    $mail->setFrom($email, $name);
    $mail->addAddress('geral@visioria.pt', 'Visioria');

    // Anexo
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == 0) {
        $mail->addAttachment($_FILES['attachment']['tmp_name'], $_FILES['attachment']['name']);
    }

    // Conteúdo
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body    = "<p><strong>Nome:</strong> $name</p>
                      <p><strong>Email:</strong> $email</p>
                      <p><strong>Mensagem:</strong><br>$message</p>";
    $mail->AltBody = "Nome: $name\nEmail: $email\nMensagem:\n$message";

    $mail->send();
    echo json_encode([
    'status' => 'success',
    'message' => 'Sua mensagem foi enviada, a Visioria agradece!'
]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => "Erro ao enviar: {$mail->ErrorInfo}"]);
}
