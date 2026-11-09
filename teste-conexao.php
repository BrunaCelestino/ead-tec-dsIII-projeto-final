<?php

$config = require __DIR__ . '/Model/config.local.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $con = new mysqli(
        $config['host'],
        $config['username'],
        $config['password'],
        $config['database'],
        $config['port']
    );

    $con->set_charset($config['charset']);

    echo "Conexão realizada com sucesso!<br>";
    echo "Banco conectado: " . $con->query("SELECT DATABASE()")->fetch_row()[0] . "<br>";

    $resultado = $con->query(
        "SELECT idusuario, nome FROM usuario ORDER BY idusuario LIMIT 1"
    );

    $usuario = $resultado->fetch_assoc();

    echo "<pre>";
    var_dump($usuario);
    echo "</pre>";

    $con->close();

} catch (Throwable $e) {
    echo "ERRO: " . htmlspecialchars($e->getMessage());
}