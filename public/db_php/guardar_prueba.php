<?php
// public/db_php/guardar_prueba.php

require_once __DIR__ . '/config.db.php';

// Le decimos al navegador que vamos a devolver un JSON (para el AJAX de Astro)
header('Content-Type: application/json');

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error de conexión con la base de datos."]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitización de los datos que llegan
    $nombre = htmlspecialchars(strip_tags($_POST['nombre'] ?? ''));
    $correo = filter_var($_POST['correo'] ?? '', FILTER_SANITIZE_EMAIL);
    $telefono = htmlspecialchars(strip_tags($_POST['telefono'] ?? ''));

    // Validación básica
    if (!empty($nombre) && filter_var($correo, FILTER_VALIDATE_EMAIL) && !empty($telefono)) {
        try {
            // Guardamos en una nueva tabla llamada 'pruebas_gratis'
            $stmt = $pdo->prepare("INSERT INTO pruebas_gratis (nombre, correo, telefono) VALUES (?, ?, ?)");
            $stmt->execute([$nombre, $correo, $telefono]);

            // Respuesta de éxito (Status 200 OK)
            http_response_code(200);
            echo json_encode(["success" => true, "mensaje" => "Datos guardados correctamente."]);
            exit;

        } catch (PDOException $e) {
            http_response_code(500);
            if ($e->getCode() == 23000) { // Error 23000 = Entrada duplicada (correo repetido)
                echo json_encode(["error" => "Este correo ya ha solicitado una prueba."]);
            } else {
                echo json_encode(["error" => "Error al insertar en la base de datos."]);
            }
            exit;
        }
    } else {
        http_response_code(400); // 400 Bad Request
        echo json_encode(["error" => "Datos inválidos o incompletos."]);
        exit;
    }
} else {
    http_response_code(405); // 405 Method Not Allowed
    echo json_encode(["error" => "Método no permitido."]);
    exit;
}
?>