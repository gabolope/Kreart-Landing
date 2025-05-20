<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Configurar cabeceras para respuesta JSON
header('Content-Type: application/json');

$mail = new PHPMailer(true);

try {
    // Validaciones básicas del lado del servidor
    if (
        empty($_POST['name']) ||
        empty($_POST['email']) ||
        empty($_POST['message'])
    ) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'Faltan campos requeridos.',
        ]);
        exit;
    }

    $userName    = htmlspecialchars($_POST['name']);
    $userEmail   = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $userSubject = htmlspecialchars($_POST['subject'] ?? 'Consulta web');
    $userMessage = nl2br(htmlspecialchars($_POST['message']));

    if (!$userEmail) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'Correo electrónico inválido.',
        ]);
        exit;
    }

    // Configuración SMTP
    $mail->isSMTP();
    $mail->Host       = '[HOST]'; // Reemplazar
    $mail->SMTPAuth   = true;
    $mail->Username   = 'contacto@kreart.com.ar';
    $mail->Password   = '[PASS]'; // Reemplazar
    $mail->SMTPSecure = 'ssl';
    $mail->Port       = 465;
    $mail->CharSet    = 'UTF-8';

    // Remitente y destinatario
    $mail->setFrom('contacto@kreart.com.ar', 'Formulario Web');
    $mail->addAddress('contacto@kreart.com.ar');
    $mail->addReplyTo($userEmail, $userName);

    // Contenido del mensaje
    $mail->isHTML(true);
    $mail->Subject = 'Formulario: ' . $userSubject;
    $mail->Body    = "
      <strong>Nombre:</strong> {$userName}<br>
      <strong>Email:</strong> {$userEmail}<br>
      <strong>Asunto:</strong> {$userSubject}<br>
      <strong>Mensaje:</strong><br>{$userMessage}
    ";

    $mail->send();

    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'message' => 'Mensaje enviado correctamente.',
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'No se pudo enviar el mensaje. Error: ' . $mail->ErrorInfo,
    ]);
}
