<?php
declare(strict_types=1);

require_once __DIR__ . '/../Model/ExperienciaProfissional.php';

/**
 * Controla o fluxo de dados da experiência profissional entre a View e o Model.
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
     * Remove somente um registro pertencente ao usuário autenticado.
     */
    public function remover(int $id, int $idusuario): bool
    {
        $experiencia = new ExperienciaProfissional();
        return $experiencia->excluirDoUsuario($id, $idusuario);
    }

    /**
     * Retorna a lista de experiências do usuário informado.
     */
    public function gerarLista(int $idusuario): array
    {
        $experiencia = new ExperienciaProfissional();
        return $experiencia->listarExperiencias($idusuario);
    }
}
