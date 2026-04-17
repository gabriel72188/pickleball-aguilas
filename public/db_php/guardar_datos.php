<?php
// public/guardar_datos.php

require_once __DIR__ . '/config.db.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión. Contacta con el administrador.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = htmlspecialchars(strip_tags($_POST['nombre'] ?? ''));
    $correo = filter_var($_POST['correo'] ?? '', FILTER_SANITIZE_EMAIL);
    $telefono = htmlspecialchars(strip_tags($_POST['telefono'] ?? ''));
    $privacidad = isset($_POST['privacidad']) ? 1 : 0;

    $idioma_origen = isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], '/en/') !== false ? 'en' : 'es';

    if (!empty($nombre) && filter_var($correo, FILTER_VALIDATE_EMAIL) && $privacidad) {
        try {
            $stmt = $pdo->prepare("INSERT INTO interesados (nombre, correo, telefono, privacidad) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nombre, $correo, $telefono, $privacidad]);

            header("Location: /$idioma_origen/contacto/?estado=exito");
            exit;

        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Error 23000 = Correo duplicado
                header("Location: /$idioma_origen/contacto/?estado=duplicado");
                exit;
            } else {
                header("Location: /$idioma_origen/contacto/?estado=error");
                exit;
            }
        }
    } else {
        header("Location: /$idioma_origen/contacto/?estado=invalido");
        exit;
    }
} else {
    header("Location: /es/");
    exit;
}
?>