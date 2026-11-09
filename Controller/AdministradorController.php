<?php
declare(strict_types=1);

require_once __DIR__ . '/../Model/Administrador.php';

class AdministradorController
{
    public function login(string $cpf, string $senha): bool
    {
        $cpf = preg_replace('/\D+/', '', $cpf) ?? '';
        if (strlen($cpf) !== 11 || $senha === '') return false;

        $administrador = new Administrador();
        if (!$administrador->carregarAdministrador($cpf)) return false;

        $senhaArmazenada = (string)$administrador->getSenha();
        $valida = password_verify($senha, $senhaArmazenada);

        // Verifica senhas cadastradas antes da adoção de criptografia e atualiza o registro após a autenticação.
        if (!$valida && hash_equals($senhaArmazenada, $senha)) {
            $valida = true;
            $administrador->atualizarSenha(password_hash($senha, PASSWORD_DEFAULT));
        }

        if (!$valida) return false;

        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        session_regenerate_id(true);
        $_SESSION['administrador_id'] = $administrador->getID();
        unset($_SESSION['usuario_id']);
        return true;
    }

    public function gerarLista(): array
    {
        return (new Administrador())->listaCadastrados();
    }

    public function gerarListaUsuarios(): array
    {
        require_once __DIR__ . '/../Model/Usuario.php';
        return (new Usuario())->listarUsuarios();
    }

    public function gerarDadosUsuario(int $idUsuario): ?array
    {
        require_once __DIR__ . '/../Model/Usuario.php';
        require_once __DIR__ . '/../Model/FormacaoAcad.php';
        require_once __DIR__ . '/../Model/ExperienciaProfissional.php';
        require_once __DIR__ . '/../Model/OutrasFormacoes.php';

        if ($idUsuario <= 0) return null;

        $usuario = new Usuario();
        if (!$usuario->carregarPorID($idUsuario)) return null;

        return [
            'usuario' => $usuario,
            'formacoes' => (new FormacaoAcad())->listarFormacoes($idUsuario),
            'experiencias' => (new ExperienciaProfissional())->listarExperiencias($idUsuario),
            'outrasFormacoes' => (new OutrasFormacoes())->listarOutrasFormacoes($idUsuario),
        ];
    }

    public function sair(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        unset($_SESSION['administrador_id']);
        session_regenerate_id(true);
    }
}
