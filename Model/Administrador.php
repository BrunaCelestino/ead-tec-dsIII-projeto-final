<?php
declare(strict_types=1);
require_once __DIR__ . '/ConexaoBD.php';

class Administrador
{
    private ?int $id = null;
    private ?string $nome = null;
    private ?string $cpf = null;
    private ?string $senha = null;

    public function setID(?int $v): void
    {
        $this->id = $v;
    }
    public function getID(): ?int
    {
        return $this->id;
    }
    public function setNome(?string $v): void
    {
        $this->nome = $v;
    }
    public function getNome(): ?string
    {
        return $this->nome;
    }
    public function setCPF(?string $v): void
    {
        $this->cpf = $v;
    }
    public function getCPF(): ?string
    {
        return $this->cpf;
    }
    public function getSenha(): ?string
    {
        return $this->senha;
    }
    public function setSenha(?string $v): void
    {
        $this->senha = $v;
    }



    public function carregarAdministrador($cpf)
    {
        require_once 'ConexaoBD.php';
        $con = new ConexaoBD();
        $conn = $con->conectar();
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $sql = "SELECT * FROM administrador WHERE cpf = " . $cpf;
        $re = $conn->query($sql);
        $r = $re->fetch_object();
        if ($r != null) {
            $this->id = $r->idadministrador;
            $this->nome = $r->nome;
            $this->cpf = $r->cpf;
            $this->senha = $r->senha;
            $conn->close();
            return true;
        } else {
            $conn->close()
        }
        return false;
    }

         public function listaCadastrados()
    {
        $con = (new ConexaoBD())->conectar();
        $result = $con->query("SELECT idadministrador, nome, cpf FROM administrador;");
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $result->free();
        $con->close();
        return $rows;
    }

   
}