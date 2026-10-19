<?php
declare(strict_types=1);
require_once __DIR__ . '/ConexaoBD.php';

class ExperienciaProfissional
{
    private ?int $id = null;
    private ?int $idusuario = null;
    private ?string $inicio = null;
    private ?string $fim = null;
    private ?string $empresa = null;
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
    public function setEmpresa(?string $v): void { $this->empresa = $v; }
    public function getEmpresa(): ?string { return $this->empresa; }
    public function setDescricao(?string $v): void { $this->descricao = $v; }
    public function getDescricao(): ?string { return $this->descricao; }
    public function getUltimoErro(): ?string { return $this->ultimoErro; }

    public function inserirBD(): bool
    {
        try {
            $con=(new ConexaoBD())->conectar();
            $stmt=$con->prepare('INSERT INTO experienciaprofissional (idusuario,inicio,fim,empresa,descricao) VALUES (?,?,?,?,?)');
            $stmt->bind_param('issss',$this->idusuario,$this->inicio,$this->fim,$this->empresa,$this->descricao);
            $stmt->execute(); $this->id=$con->insert_id; $stmt->close(); $con->close();
            $this->ultimoErro=null; return true;
        } catch (Throwable $e) { $this->ultimoErro=$e->getMessage(); return false; }
    }
    public function alterarBD(): bool
    {
        if ($this->id===null) { $this->ultimoErro='ID da experiência obrigatório.'; return false; }
        try {
            $con=(new ConexaoBD())->conectar();
            $stmt=$con->prepare('UPDATE experienciaprofissional SET idusuario=?,inicio=?,fim=?,empresa=?,descricao=? WHERE idexperienciaprofissional=?');
            $stmt->bind_param('issssi',$this->idusuario,$this->inicio,$this->fim,$this->empresa,$this->descricao,$this->id);
            $stmt->execute(); $stmt->close(); $con->close(); $this->ultimoErro=null; return true;
        } catch (Throwable $e) { $this->ultimoErro=$e->getMessage(); return false; }
    }
    public function excluirBD(?int $id=null): bool
    {
        $id=$id??$this->id;
        if ($id===null) { $this->ultimoErro='ID da experiência obrigatório.'; return false; }
        try {
            $con=(new ConexaoBD())->conectar();
            $stmt=$con->prepare('DELETE FROM experienciaprofissional WHERE idexperienciaprofissional=?');
            $stmt->bind_param('i',$id); $stmt->execute(); $stmt->close(); $con->close();
            $this->ultimoErro=null; return true;
        } catch (Throwable $e) { $this->ultimoErro=$e->getMessage(); return false; }
    }
    public function listarExperiencias(int $idusuario): array
    {
        $con=(new ConexaoBD())->conectar();
        $stmt=$con->prepare('SELECT idexperienciaprofissional,idusuario,inicio,fim,empresa,descricao FROM experienciaprofissional WHERE idusuario=? ORDER BY inicio DESC');
        $stmt->bind_param('i',$idusuario); $stmt->execute();
        $rows=$stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close(); $con->close(); return $rows;
    }
}