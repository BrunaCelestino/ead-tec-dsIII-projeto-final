<?php
declare(strict_types=1);
require_once __DIR__ . '/ConexaoBD.php';

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

    public function inserirBD(): bool
    {
        try {
            $con=(new ConexaoBD())->conectar();
            $stmt=$con->prepare('INSERT INTO outrasformacoes (idusuario,inicio,fim,descricao) VALUES (?,?,?,?)');
            $stmt->bind_param('isss',$this->idusuario,$this->inicio,$this->fim,$this->descricao);
            $stmt->execute(); $this->id=$con->insert_id; $stmt->close(); $con->close();
            $this->ultimoErro=null; return true;
        } catch (Throwable $e) { $this->ultimoErro=$e->getMessage(); return false; }
    }
    public function alterarBD(): bool
    {
        if ($this->id===null) { $this->ultimoErro='ID da formacao obrigatorio.'; return false; }
        try {
            $con=(new ConexaoBD())->conectar();
            $stmt=$con->prepare('UPDATE outrasformacoes SET idusuario=?,inicio=?,fim=?,descricao=? WHERE idoutrasformacoes=?');
            $stmt->bind_param('isssi',$this->idusuario,$this->inicio,$this->fim,$this->descricao,$this->id);
            $stmt->execute(); $stmt->close(); $con->close(); $this->ultimoErro=null; return true;
        } catch (Throwable $e) { $this->ultimoErro=$e->getMessage(); return false; }
    }
    public function excluirBD(?int $id=null): bool
    {
        $id=$id??$this->id;
        if ($id===null) { $this->ultimoErro='ID da formacao obrigatorio.'; return false; }
        try {
            $con=(new ConexaoBD())->conectar();
            $stmt=$con->prepare('DELETE FROM outrasformacoes WHERE idoutrasformacoes=?');
            $stmt->bind_param('i',$id); $stmt->execute(); $stmt->close(); $con->close();
            $this->ultimoErro=null; return true;
        } catch (Throwable $e) { $this->ultimoErro=$e->getMessage(); return false; }
    }
    public function listarOutrasFormacoes(int $idusuario): array
    {
        $con=(new ConexaoBD())->conectar();
        $stmt=$con->prepare('SELECT idoutrasformacoes,idusuario,inicio,fim,descricao FROM outrasformacoes WHERE idusuario=? ORDER BY inicio DESC');
        $stmt->bind_param('i',$idusuario); $stmt->execute();
        $rows=$stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close(); $con->close(); return $rows;
    }
}