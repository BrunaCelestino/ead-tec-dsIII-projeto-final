<?php
declare(strict_types=1);
final class ConexaoBD
{
    private array $config;
    public function __construct(?array $config = null)
    {
        if ($config === null) {
            $file = __DIR__ . DIRECTORY_SEPARATOR . 'config.local.php';
            if (!is_file($file)) {
                throw new RuntimeException('Configure o acesso ao MySQL em config.local.php.');
            }
            $config = require $file;
        }
        foreach (['host','port','database','username','password','charset'] as $key) {
            if (!array_key_exists($key, $config)) {
                throw new InvalidArgumentException('Configuração MySQL ausente: ' . $key);
            }
        }
        $this->config = $config;
    }
    public function conectar(): mysqli
    {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $con = new mysqli(
            (string)$this->config['host'],
            (string)$this->config['username'],
            (string)$this->config['password'],
            (string)$this->config['database'],
            (int)$this->config['port']
        );
        $con->set_charset((string)$this->config['charset']);
        return $con;
    }
}