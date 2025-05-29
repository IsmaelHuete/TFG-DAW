<?php
    session_start();
    //incluye el archivo de configuración de la base de datos
    require_once __DIR__ . '/../config/Conexion_BBDD.php';
    //verifica si el usuario está autenticado sino redirige a la página de login
    if (!isset($_SESSION['email'])) {
        header('Location: /login');
        exit;
    }

    //obtiene el plan seleccionado del formulario, si no recibio nada usa mensual como valor por defecto
    $plan = $_POST['plan'] ?? 'mensual';

    //verifica si el plan es mensual o anual, si no es ninguno de los dos, redirige a la página de premiumkl
    $stmt = $pdo->prepare("SELECT id_usuario FROM usuario WHERE email = ?");
    $stmt->execute([$_SESSION['email']]);
    $id_usuario = $stmt->fetchColumn();

    if (!$id_usuario) {
        die("Usuario no encontrado");
    }


    //si el plan es gratuito pues updatea el tipo de plan a premium
    $stmt = $pdo->prepare("
        UPDATE usuario
        SET tipo_plan = 'premium'
        WHERE id_usuario = ?
    ");
    $stmt->execute([$id_usuario]);

    //redirige a la página de pago exitoso donde se mostrará el mensaje de éxito y se enviará un correo al usuario
    header("Location: /pago_exito");
    exit;
    
/*
Ejemplo de uso:
- El usuario pulsa el botón de pagar en la página de checkout y lo redirige a stripe.
- Se envía un formulario POST a pagar.php con el campo 'plan' (por ejemplo, 'mensual' o 'anual').
- Este script actualiza el plan del usuario a premium y lo redirige a /pago_exito.
*/