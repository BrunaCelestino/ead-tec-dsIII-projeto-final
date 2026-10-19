<?php
declare(strict_types=1);
require_once __DIR__ . '/ConexaoBD.php';

class Usuario
{
    private ?int $id = null;
    private ?string $nome = null;
    private ?string $cpf = null;
    private ?string $dataNascimento = null;
    private ?string $email = null;
    private ?string $senha = null;
    private ?string $ultimoErro = null;

    public function setID(?int $v): void { $this->id = $v; }
    public function getID(): ?int { return $this->id; }
    public function setNome(?string $v): void { $this->nome = $v; }
    public function getNome(): ?string { return $this->nome; }
    public function setCPF(?string $v): void { $this->cpf = $v; }
    public function getCPF(): ?string { return $this->cpf; }
    public function setDataNascimento(?string $v): void { $this->dataNascimento = $v; }
    public function getDataNascimento(): ?string { return $this->dataNascimento; }
    public function setEmail(?string $v): void { $this->email = $v; }
    public function getEmail(): ?string { return $this->email; }
    public function setSenha(?string $v): void { $this->senha = $v; }
    public function getUltimoErro(): ?string { return $this->ultimoErro; }

    public function inserirBD(): bool
    {
        try {
            $con = (new ConexaoBD())->conectar();
            $hash = $this->senha === null ? null : password_hash($this->senha, PASSWORD_DEFAULT);
            $stmt = $con->prepare('INSERT INTO usuario (nome, cpf, dataNascimento, email, senha) VALUES (?, ?, ?, ?, ?)');
            $stmt->bind_param('sssss', $this->nome, $this->cpf, $this->dataNascimento, $this->email, $hash);
            $stmt->execute();
            $this->id = $con->insert_id;
            $stmt->close();
            $con->close();
            $this->ultimoErro = null;
            return true;
        } catch (Throwable $e) {
            $this->ultimoErro = $e->getMessage();
            return false;
        }
    }

    public function consultarBD(string $email): bool
    {
        try {
            $con = (new ConexaoBD())->conectar();
            $stmt = $con->prepare('SELECT idusuario,nome,cpf,dataNascimento,email,senha FROM usuario WHERE email = ? LIMIT 1');
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            $con->close();
            if (!$row) { $this->ultimoErro = null; return false; }
            $this->id = (int)$row['idusuario'];
            $this->nome = $row['nome'];
            $this->cpf = $row['cpf'];
            $this->dataNascimento = $row['dataNascimento'];
            $this->email = $row['email'];
            $this->senha = $row['senha'];
            $this->ultimoErro = null;
            return true;
        } catch (Throwable $e) {
            $this->ultimoErro = $e->getMessage();
            return false;
        }
    }

    
    public function carregarUsuario(string $cpf): bool
    {
        try {
            $con = (new ConexaoBD())->conectar();
            $stmt = $con->prepare('SELECT idusuario,nome,cpf,dataNascimento,email,senha FROM usuario WHERE cpf = ? LIMIT 1');
            $stmt->bind_param('s', $cpf);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            $con->close();
            if (!$row) { $this->ultimoErro = null; return false; }
            $this->id = (int)$row['idusuario'];
            $this->nome = $row['nome'];
            $this->cpf = $row['cpf'];
            $this->dataNascimento = $row['dataNascimento'];
            $this->email = $row['email'];
            $this->senha = $row['senha'];
            $this->ultimoErro = null;
            return true;
        } catch (Throwable $e) {
            $this->ultimoErro = $e->getMessage();
            return false;
        }
    }

    public function validarSenha(string $senha): bool
    {
        return $this->senha !== null && password_verify($senha, $this->senha);
    }

    public function alterarBD(): bool
    {
        if ($this->id === null) { $this->ultimoErro = 'ID do usuário obrigatório.'; return false; }
        try {
            $con = (new ConexaoBD())->conectar();
            if ($this->senha !== null && $this->senha !== '') {
                $hash = password_hash($this->senha, PASSWORD_DEFAULT);
                $stmt = $con->prepare('UPDATE usuario SET nome=?,cpf=?,dataNascimento=?,email=?,senha=? WHERE idusuario=?');
                $stmt->bind_param('sssssi', $this->nome, $this->cpf, $this->dataNascimento, $this->email, $hash, $this->id);
            } else {
                $stmt = $con->prepare('UPDATE usuario SET nome=?,cpf=?,dataNascimento=?,email=? WHERE idusuario=?');
                $stmt->bind_param('ssssi', $this->nome, $this->cpf, $this->dataNascimento, $this->email, $this->id);
            }
            $stmt->execute(); $stmt->close(); $con->close();
            $this->ultimoErro = null; return true;
        } catch (Throwable $e) { $this->ultimoErro = $e->getMessage(); return false; }
    }

    public function excluirBD(?int $id = null): bool
    {
        $id = $id ?? $this->id;
        if ($id === null) { $this->ultimoErro = 'ID do usuário obrigatório.'; return false; }
        try {
            $con = (new ConexaoBD())->conectar();
            $stmt = $con->prepare('DELETE FROM usuario WHERE idusuario=?');
            $stmt->bind_param('i', $id); $stmt->execute(); $stmt->close(); $con->close();
            $this->ultimoErro = null; return true;
        } catch (Throwable $e) { $this->ultimoErro = $e->getMessage(); return false; }
    }

    public function listarUsuarios(): array
    {
        $con = (new ConexaoBD())->conectar();
        $result = $con->query('SELECT idusuario,nome,cpf,dataNascimento,email FROM usuario ORDER BY nome');
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $result->free(); $con->close();
        return $rows;
    }
}