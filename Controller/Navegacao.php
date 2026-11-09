<?php
declare(strict_types=1);

require_once __DIR__ . '/PortalController.php';

// O index.php é o único ponto de entrada; a área é definida pela query string.
$isAdminArea = (($_GET['area'] ?? '') === 'admin');

if ($isAdminArea) {
    require_once __DIR__ . '/AdministradorController.php';

    $adminLogado = !empty($_SESSION['administrador_id']);
    $paginaAdmin = (string)($_GET['pagina'] ?? ($adminLogado ? 'principal' : 'login'));

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $acaoAdmin = (string)($_POST['acao_admin'] ?? '');
        if (!portalTokenValido()) {
            portalMensagem('Sessão expirada ou solicitação inválida. Tente novamente.', 'erro');
            portalRedirecionar('index.php?area=admin');
        }

        $controllerAdmin = new AdministradorController();
        if ($acaoAdmin === 'login') {
            $cpf = (string)($_POST['cpf_admin'] ?? '');
            $senha = (string)($_POST['senha_admin'] ?? '');
            if ($controllerAdmin->login($cpf, $senha)) {
                portalMensagem('Acesso administrativo realizado com sucesso.');
                portalRedirecionar('index.php?area=admin&pagina=principal');
            }
            portalMensagem('CPF ou senha de administrador inválidos.', 'erro');
            portalRedirecionar('index.php?area=admin');
        }

        if ($acaoAdmin === 'sair') {
            $controllerAdmin->sair();
            portalMensagem('Você saiu da área administrativa.');
            portalRedirecionar('index.php?area=admin');
        }
    }

    $adminLogado = !empty($_SESSION['administrador_id']);
    if ($paginaAdmin !== 'login' && !$adminLogado) {
        portalMensagem('Entre com sua conta de administrador para continuar.', 'erro');
        portalRedirecionar('index.php?area=admin');
    }
    if ($paginaAdmin === 'login' && $adminLogado) {
        portalRedirecionar('index.php?area=admin&pagina=principal');
    }
    if (!in_array($paginaAdmin, ['login', 'principal', 'usuarios', 'administradores'], true)) {
        $paginaAdmin = $adminLogado ? 'principal' : 'login';
    }

    $flash = $_SESSION['portal_flash'] ?? null;
    unset($_SESSION['portal_flash']);
    $csrf = portalToken();

    if ($paginaAdmin === 'login') {
        require __DIR__ . '/../View/administracao/ADMLogin.php';
    } elseif ($paginaAdmin === 'principal') {
        require __DIR__ . '/../View/administracao/ADMPrincipal.php';
    } elseif ($paginaAdmin === 'usuarios') {
        $usuariosCadastrados = (new AdministradorController())->gerarListaUsuarios();
        require __DIR__ . '/../View/administracao/ADMListarCadastrados.php';
    } else {
        $administradoresCadastrados = (new AdministradorController())->gerarLista();
        require __DIR__ . '/../View/administracao/ADMListarAdministradores.php';
    }
    return;
}

$pagina = (string)($_GET['pagina'] ?? (isset($_SESSION['usuario_id']) ? 'principal' : 'login'));
$usuarioLogado = portalUsuarioAtual();

if ($pagina === 'principal' && !$usuarioLogado) portalRedirecionar('index.php');
if ($pagina === 'login' && $usuarioLogado) portalRedirecionar('index.php?pagina=principal');

$paginasPublicas = ['login', 'primeiro-acesso'];
if (!in_array($pagina, $paginasPublicas, true) && $pagina !== 'principal') {
    $pagina = $usuarioLogado ? 'principal' : 'login';
}

$flash = $_SESSION['portal_flash'] ?? null;
unset($_SESSION['portal_flash']);
$csrf = portalToken();

if ($pagina === 'primeiro-acesso' && !$usuarioLogado) {
    require __DIR__ . '/../View/primeiroAcesso.php';
} elseif ($pagina === 'principal' && $usuarioLogado) {
    $idUsuario = (int)$usuarioLogado->getID();
    $formacoes = (new FormacaoAcad())->listarFormacoes($idUsuario);
    $experiencias = (new ExperienciaProfissionalController())->gerarLista($idUsuario);
    $outrasFormacoes = (new OutrasFormacoes())->listarOutrasFormacoes($idUsuario);
    require __DIR__ . '/../View/principal.php';
} else {
    require __DIR__ . '/../View/login.php';
}
