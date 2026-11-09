<?php
declare(strict_types=1);

/*
 * Responsável exclusivamente por decidir qual tela será apresentada.
 * O processamento de formulários permanece no PortalController.
 */
require_once __DIR__ . '/PortalController.php';

$pagina = (string)($_GET['pagina'] ?? (isset($_SESSION['usuario_id']) ? 'principal' : 'login'));
$usuarioLogado = portalUsuarioAtual();

// Impede acesso à área autenticada sem uma sessão válida.
if ($pagina === 'principal' && !$usuarioLogado) {
    portalRedirecionar('index.php');
}

// Usuário autenticado não precisa voltar à tela de login.
if ($pagina === 'login' && $usuarioLogado) {
    portalRedirecionar('index.php?pagina=principal');
}

// Aceita somente páginas conhecidas.
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
