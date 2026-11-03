<?php
declare(strict_types=1);
require_once __DIR__ . '/ConexaoBD.php';

/**
 * Operações de persistência das outras formações de um usuário.
 */
class OutrasFormacoes
{
    private ?int $id = null;
    private ?int $idusuario = null;
    private ?string $inicio = null;
    private ?string $fim = null;
    private ?string $descricao = null;
    private ?string $ultimoErro = null;

    public function setID(?int $v): void { $this->id = $v; }
    public function getID(): ?int { return $this->id; }
    public function setIDUsuario(?int $v): void { $this->idusuario = $v; }
    public function getIDUsuario(): ?int { return $this->idusuario; }
    public function setInicio(?string $v): void { $this->inicio = $v; }
    public function getInicio(): ?string { return $this->inicio; }
    public function setFim(?string $v): void { $this->fim = $v; }
    public function getFim(): ?string { return $this->fim; }
    public function setDescricao(?string $v): void { $this->descricao = $v; }
    public function getDescricao(): ?string { return $this->descricao; }
    public function getUltimoErro(): ?string { return $this->ultimoErro; }

    private function validar(): bool
    {
        if ($this->idusuario === null || $this->idusuario <= 0) {
            $this->ultimoErro = 'Usuário inválido.';
            return false;
        }
        if ($this->inicio !== null && $this->fim !== null && $this->fim < $this->inicio) {
            $this->ultimoErro = 'A data final não pode ser anterior à data inicial.';
            return false;
        }
        if ($this->descricao === null || trim($this->descricao) === '') {
            $this->ultimoErro = 'Informe a descrição da formação.';
            return false;
        }
        return true;
    }

    public function inserirBD(): bool
    {
        if (!$this->validar()) { return false; }
        try {
            $con = (new ConexaoBD())->conectar();
            $stmt = $con->prepare('INSERT INTO outrasformacoes (idusuario, inicio, fim, descricao) VALUES (?, ?, ?, ?)');
            $stmt->bind_param('isss', $this->idusuario, $this->inicio, $this->fim, $this->descricao);
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

    public function alterarBD(): bool
    {
        if ($this->id === null) { $this->ultimoErro = 'ID da formação obrigatório.'; return false; }
        if (!$this->validar()) { return false; }
        try {
            $con = (new ConexaoBD())->conectar();
            $stmt = $con->prepare('UPDATE outrasformacoes SET inicio=?, fim=?, descricao=? WHERE idoutrasformacoes=? AND idusuario=?');
            $stmt->bind_param('sssii', $this->inicio, $this->fim, $this->descricao, $this->id, $this->idusuario);
            $stmt->execute();
            $alterou = $stmt->affected_rows >= 0;
            $stmt->close();
            $con->close();
            $this->ultimoErro = null;
            return $alterou;
        } catch (Throwable $e) {
            $this->ultimoErro = $e->getMessage();
            return false;
        }
    }

    public function excluirBD(?int $id = null): bool
    {
        $id = $id ?? $this->id;
        if ($id === null || $id <= 0 || $this->idusuario === null) {
            $this->ultimoErro = 'Informe uma formação e um usuário válidos.';
            return false;
        }
        try {
            $con = (new ConexaoBD())->conectar();
            $stmt = $con->prepare('DELETE FROM outrasformacoes WHERE idoutrasformacoes = ? AND idusuario = ?');
            $stmt->bind_param('ii', $id, $this->idusuario);
            $stmt->execute();
            $removido = $stmt->affected_rows === 1;
            $stmt->close();
            $con->close();
            $this->ultimoErro = $removido ? null : 'Formação não encontrada para este usuário.';
            return $removido;
        } catch (Throwable $e) {
            $this->ultimoErro = $e->getMessage();
            return false;
        }
    }

    public function listarOutrasFormacoes(int $idusuario): array
    {
        if ($idusuario <= 0) { $this->ultimoErro = 'Usuário inválido.'; return []; }
        try {
            $con = (new ConexaoBD())->conectar();
            $stmt = $con->prepare('SELECT idoutrasformacoes, idusuario, inicio, fim, descricao FROM outrasformacoes WHERE idusuario = ? ORDER BY inicio DESC, idoutrasformacoes DESC');
            $stmt->bind_param('i', $idusuario);
            $stmt->execute();
            $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            $con->close();
            $this->ultimoErro = null;
            return $rows;
        } catch (Throwable $e) {
            $this->ultimoErro = $e->getMessage();
            return [];
        }
    }
}
