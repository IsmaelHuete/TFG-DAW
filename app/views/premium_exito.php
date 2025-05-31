<?php
session_start();

// Importa las clases de PHPMailer para enviar correos
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../config/Conexion_BBDD.php';
require_once __DIR__ . '/../../app/models/usuario.php';

if (!isset($_SESSION['email'])) {
    header('Location: /login');
    exit;
}

$email = $_SESSION['email'];
$usuarioModel = new Usuario($pdo);

// Crea una nueva instancia de PHPMailer para enviar el correo de confirmación
$mail = new PHPMailer(true);

try {
    // Configuración del servidor SMTP de Gmail
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';        
    $mail->SMTPAuth   = true;
    $mail->Username   = 'tfgDaw2025@gmail.com';    
    $mail->Password   = 'mvgv wzpg tomp fuwj';         
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    // Configuración del remitente y destinatario
    $mail->setFrom('tfgDaw2025@gmail.com', 'Musicfy');
    $mail->addAddress($email);  // Email del usuario

    // Configuración del contenido del correo
    $mail->isHTML(true);
    $mail->Subject = '🎧 ¡Ya eres Premium en Musicfy!';
    $mail->Body    = '
    <div style="font-family: Poppins, sans-serif; background: #f9f9f9; padding: 30px; color: #333;">
        <div style="max-width: 600px; margin: auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
            <h1 style="color: #8839ef; text-align:center;">¡Gracias por unirte a Musicfy Premium!</h1>
            <p style="font-size: 16px; line-height: 1.5;">
                Hola <strong>' . htmlspecialchars($_SESSION['nombre']) . '</strong>,<br><br>
                Hemos actualizado tu cuenta correctamente al plan <strong>Premium</strong>. A partir de ahora disfrutarás de:
            </p>
            <ul style="font-size: 16px; line-height: 1.8;">
                <li>✅ Reproducción sin anuncios</li>
                <li>✅ Calidad de sonido mejorada</li>
                <li>✅ Acceso ilimitado</li>
                <li>✅ Compatibilidad en múltiples dispositivos</li>
            </ul>
            <p style="font-size: 16px; margin-top: 30px;">
                Esperamos que disfrutes al máximo tu nueva experiencia musical. <br><br>
                Si tienes dudas, puedes contactar con nosotros en <a href="mailto:soporte@musicfy.com" style="color: #8839ef;">soporte@musicfy.com</a>.
            </p>
            <div style="text-align:center; margin-top: 40px;">
                <a href="http://localhost:3000/home" style="background: #8839ef; color: white; text-decoration: none; padding: 12px 25px; border-radius: 30px; display:inline-block;">Ir a Musicfy</a>
            </div>
            <p style="text-align:center; margin-top: 40px; font-size: 14px; color: #777;">
                © ' . date('Y') . ' Musicfy. Todos los derechos reservados.
            </p>
        </div>
    </div>';

    $mail->send();
} catch (Exception $e) {
    error_log("No se pudo enviar el correo de confirmación: {$mail->ErrorInfo}");
}


// Cambia el plan a "premium"
$usuarioModel->actualizarPlan($email, 'premium');

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pago Exitoso</title>
    <link rel="stylesheet" href="css/comun.css">
    <link rel="stylesheet" href="css/premium_exito.css">
    <link rel="stylesheet" href="css/header1.css">
    <link rel="stylesheet" href="css/footer.css">
</head>
<body>
    <?php 
        include ("layouts/header1.php");
        
    ?>
    <main style=" text-align:center;">
        <h1>✅ ¡Gracias por tu compra!</h1>
        <p>Tu cuenta ha sido actualizada a <strong>Premium</strong>.</p>
        <a href="/index">Volver al inicio</a>
    </main>
    <?php 
        include ("layouts/footer.php");
    ?>
</body>
</html>