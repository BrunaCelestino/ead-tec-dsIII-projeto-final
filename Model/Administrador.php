<?php
declare(strict_types=1);

require_once __DIR__ . '/ConexaoBD.php';

class Administrador
{
    private ?int $id = null;
    private ?string $nome = null;
    private ?string $cpf = null;
    private ?string $senha = null;

    public function getID(): ?int { return $this->id; }
    public function setID(?int $id): void { $this->id = $id; }
    public function getNome(): ?string { return $this->nome; }
    public function setNome(?string $nome): void { $this->nome = $nome; }
    public function getCPF(): ?string { return $this->cpf; }
    public function setCPF(?string $cpf): void { $this->cpf = $cpf; }
    public function getSenha(): ?string { return $this->senha; }
    public function setSenha(?string $senha): void { $this->senha = $senha; }

    public function carregarAdministrador(string $cpf): bool
    {
        try {
            $con = (new ConexaoBD())->conectar();
            $stmt = $con->prepare('SELECT idadministrador, nome, cpf, senha FROM administrador WHERE cpf = ? LIMIT 1');
            $stmt->bind_param('s', $cpf);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            $con->close();

            if (!$row) return false;
            $this->id = (int)$row['idadministrador'];
            $this->nome = $row['nome'];
            $this->cpf = $row['cpf'];
            $this->senha = $row['senha'];
            return true;
        } catch (Throwable $e) {
            error_log('Erro ao carregar administrador: ' . $e->getMessage());
            return false;
        }
    }

    public function atualizarSenha(string $senhaHash): bool
    {
        if ($this->id === null) return false;
        try {
            $con = (new ConexaoBD())->conectar();
            $stmt = $con->prepare('UPDATE administrador SET senha = ? WHERE idadministrador = ?');
            $stmt->bind_param('si', $senhaHash, $this->id);
            $stmt->execute();
            $stmt->close();
            $con->close();
            $this->senha = $senhaHash;
            return true;
        } catch (Throwable $e) {
            error_log('Erro ao atualizar senha do administrador: ' . $e->getMessage());
            return false;
        }
    }

    public function listaCadastrados(): array
    {
        try {
            $con = (new ConexaoBD())->conectar();
            $result = $con->query('SELECT idadministrador, nome, cpf FROM administrador ORDER BY nome');
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            $con->close();
            return $rows;
        } catch (Throwable $e) {
            error_log('Erro ao listar administradores: ' . $e->getMessage());
            return [];
        }
    }
}
