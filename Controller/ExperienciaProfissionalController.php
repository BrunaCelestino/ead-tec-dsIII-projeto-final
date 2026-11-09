<?php
declare(strict_types=1);

require_once __DIR__ . '/../Model/ExperienciaProfissional.php';

/**
 * Gerencia as operações de experiência profissional do usuário.
 */
class ExperienciaProfissionalController
{
    public function inserir(
        string $inicio,
        string $fim,
        string $empresa,
        string $descricao,
        int $idusuario
    ): bool {
        $experiencia = new ExperienciaProfissional();
        $experiencia->setInicio($inicio);
        $experiencia->setFim($fim);
        $experiencia->setEmpresa(trim($empresa));
        $experiencia->setDescricao(trim($descricao));
        $experiencia->setIDUsuario($idusuario);

        return $experiencia->inserirBD();
    }

    /**
     * Exclui uma experiência vinculada ao usuário informado.
     */
    public function remover(int $id, int $idusuario): bool
    {
        $experiencia = new ExperienciaProfissional();
        return $experiencia->excluirDoUsuario($id, $idusuario);
    }

    /**
     * Consulta as experiências profissionais do usuário.
     */
    public function gerarLista(int $idusuario): array
    {
        $experiencia = new ExperienciaProfissional();
        return $experiencia->listarExperiencias($idusuario);
    }
}
