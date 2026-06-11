<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'vendor/autoload.php';

function dispararEmail($assunto, $mensagemHtml) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = getenv('SMTP_HOST');
        $mail->SMTPAuth   = true;
        $mail->Username   = getenv('SMTP_USER');
        $mail->Password   = getenv('SMTP_PASS');
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = getenv('SMTP_PORT');

        $mail->setFrom(getenv('SMTP_USER'), 'Sistema de Receitas');
        $mail->addAddress(getenv('SMTP_USER'), 'Administrador'); 

        $mail->isHTML(true);
        $mail->Subject = $assunto;
        $mail->Body    = $mensagemHtml;

        $mail->send();
        return true;
    } catch (Exception $e) {
        // Em um sistema real, você pode salvar esse erro num log
        error_log("O e-mail não pôde ser enviado. Erro do Mailer: {$mail->ErrorInfo}");
        return false;
    }
}
?>