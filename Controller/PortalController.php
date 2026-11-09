<?php
declare(strict_types=1);

require_once __DIR__ . '/../Model/Usuario.php';
require_once __DIR__ . '/../Model/FormacaoAcad.php';
require_once __DIR__ . '/../Model/ExperienciaProfissional.php';
require_once __DIR__ . '/../Model/OutrasFormacoes.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function portalRedirecionar(string $destino = 'index.php'): never {
    header('Location: ' . $destino, true, 303);
    exit;
}
function portalMensagem(string $texto, string $tipo = 'sucesso'): void {
    $_SESSION['portal_flash'] = ['texto' => $texto, 'tipo' => $tipo];
}
function portalTokenValido(): bool {
    return isset($_SESSION['portal_csrf'], $_POST['csrf_token'])
        && is_string($_POST['csrf_token'])
        && hash_equals($_SESSION['portal_csrf'], $_POST['csrf_token']);
}
function portalToken(): string {
    if (empty($_SESSION['portal_csrf'])) $_SESSION['portal_csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['portal_csrf'];
}
function portalUsuarioAtual(): ?Usuario {
    $id = filter_var($_SESSION['usuario_id'] ?? null, FILTER_VALIDATE_INT);
    if (!$id) return null;
    $usuario = new Usuario();
    if (!$usuario->carregarPorID((int)$id)) return null;
    return $usuario;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = (string)($_POST['acao'] ?? '');
    if ($acao === 'login') {
        $cpf = preg_replace('/\D+/', '', (string)($_POST['txtLogin'] ?? ''));
        $senha = (string)($_POST['txtSenha'] ?? '');
        $usuario = new Usuario();
        if ($cpf !== '' && $usuario->carregarUsuario($cpf) && $usuario->validarSenha($senha)) {
            session_regenerate_id(true);
            $_SESSION['usuario_id'] = $usuario->getID();
            portalMensagem('Login realizado com sucesso.');
            portalRedirecionar('index.php?pagina=principal');
        }
        portalMensagem('CPF ou senha inválidos.', 'erro');
        portalRedirecionar('index.php');
    }

    if ($acao === 'cadastro') {
        if (!portalTokenValido()) { portalMensagem('Sessão expirada. Tente novamente.', 'erro'); portalRedirecionar('index.php?pagina=primeiro-acesso'); }
        $nome = trim((string)($_POST['txtNome'] ?? ''));
        $cpf = preg_replace('/\D+/', '', (string)($_POST['txtCPF'] ?? ''));
        $email = trim((string)($_POST['txtEmail'] ?? ''));
        $senha = (string)($_POST['txtSenha'] ?? '');
        $data = trim((string)($_POST['txtData'] ?? ''));
        if ($nome === '' || strlen($cpf) !== 11 || $senha === '' || ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL))) {
            portalMensagem('Preencha nome, CPF com 11 dígitos, senha e um e-mail válido.', 'erro');
            portalRedirecionar('index.php?pagina=primeiro-acesso');
        }
        $usuario = new Usuario();
        $usuario->setNome($nome); $usuario->setCPF($cpf);
        $usuario->setEmail($email !== '' ? $email : null);
        $usuario->setSenha($senha);
        $usuario->setDataNascimento($data !== '' ? $data : null);
        if ($usuario->inserirBD()) {
            portalMensagem('Cadastro realizado com sucesso. Faça seu login.');
            portalRedirecionar('index.php');
        }
        portalMensagem('Não foi possível realizar o cadastro. Verifique se o CPF já está cadastrado.', 'erro');
        portalRedirecionar('index.php?pagina=primeiro-acesso');
    }

    if (!portalTokenValido()) { portalMensagem('Solicitação inválida ou sessão expirada.', 'erro'); portalRedirecionar('index.php?pagina=principal'); }
    $usuario = portalUsuarioAtual();
    if (!$usuario) { unset($_SESSION['usuario_id']); portalRedirecionar('index.php'); }
    $idUsuario = (int)$usuario->getID();

    if ($acao === 'sair') { $_SESSION = []; session_regenerate_id(true); portalRedirecionar('index.php'); }

    if ($acao === 'atualizar-dados') {
        $usuario->setNome(trim((string)($_POST['txtNome'] ?? '')));
        $usuario->setCPF(preg_replace('/\D+/', '', (string)($_POST['txtCPF'] ?? '')));
        $usuario->setDataNascimento((string)($_POST['txtData'] ?? ''));
        $email = trim((string)($_POST['txtEmail'] ?? ''));
        $usuario->setEmail($email !== '' ? $email : null);
        if ($usuario->getNome() !== '' && strlen((string)$usuario->getCPF()) === 11 && ($email === '' || filter_var($email, FILTER_VALIDATE_EMAIL)) && $usuario->alterarBD()) portalMensagem('Dados pessoais atualizados com sucesso.');
        else portalMensagem('Não foi possível atualizar os dados. Confira os campos e se o CPF já está em uso.', 'erro');
    } elseif (in_array($acao, ['adicionar-formacao','adicionar-experiencia','adicionar-outra-formacao'], true)) {
        $inicio = (string)($_POST['txtInicio'] ?? '');
        $fim = (string)($_POST['txtFim'] ?? '');
        $descricao = trim((string)($_POST['txtDescricao'] ?? ''));
        $di = DateTime::createFromFormat('!Y-m-d', $inicio);
        $df = DateTime::createFromFormat('!Y-m-d', $fim);
        if (!$di || !$df || $di->format('Y-m-d') !== $inicio || $df->format('Y-m-d') !== $fim || $df < $di || $descricao === '') {
            portalMensagem('Informe datas válidas (a data final não pode ser anterior à inicial) e a descrição.', 'erro');
        } else {
            if ($acao === 'adicionar-formacao') {
                $m = new FormacaoAcad(); $m->setIDUsuario($idUsuario); $m->setInicio($inicio); $m->setFim($fim); $m->setDescricao($descricao);
                $ok = $m->inserirBD();
            } elseif ($acao === 'adicionar-experiencia') {
                $empresa = trim((string)($_POST['txtEmpresa'] ?? ''));
                $m = new ExperienciaProfissional(); $m->setIDUsuario($idUsuario); $m->setInicio($inicio); $m->setFim($fim); $m->setEmpresa($empresa); $m->setDescricao($descricao);
                $ok = $empresa !== '' && $m->inserirBD();
            } else {
                $m = new OutrasFormacoes(); $m->setIDUsuario($idUsuario); $m->setInicio($inicio); $m->setFim($fim); $m->setDescricao($descricao);
                $ok = $m->inserirBD();
            }
            portalMensagem($ok ? 'Informação inserida com sucesso.' : 'Operação não realizada. Confira os dados e a configuração do banco.', $ok ? 'sucesso' : 'erro');
        }
    } elseif (in_array($acao, ['excluir-formacao','excluir-experiencia','excluir-outra-formacao'], true)) {
        $id = filter_var($_POST['idRegistro'] ?? null, FILTER_VALIDATE_INT);
        $ok = false;
        if ($id && $id > 0) {
            if ($acao === 'excluir-formacao') { $m = new FormacaoAcad(); $ok = $m->excluirDoUsuario((int)$id, $idUsuario); }
            elseif ($acao === 'excluir-experiencia') { $m = new ExperienciaProfissional(); $ok = $m->excluirDoUsuario((int)$id, $idUsuario); }
            else { $m = new OutrasFormacoes(); $ok = $m->excluirDoUsuario((int)$id, $idUsuario); }
        }
        portalMensagem($ok ? 'Informação excluída com sucesso.' : 'Operação não realizada. Registro não encontrado.', $ok ? 'sucesso' : 'erro');
    }
    portalRedirecionar('index.php?pagina=principal');
}

$pagina = (string)($_GET['pagina'] ?? (isset($_SESSION['usuario_id']) ? 'principal' : 'login'));
$usuarioLogado = portalUsuarioAtual();
if ($pagina === 'principal' && !$usuarioLogado) portalRedirecionar('index.php');
if ($pagina !== 'principal' && $usuarioLogado && $pagina === 'login') portalRedirecionar('index.php?pagina=principal');
$flash = $_SESSION['portal_flash'] ?? null; unset($_SESSION['portal_flash']);
$csrf = portalToken();
if ($pagina === 'primeiro-acesso') require __DIR__ . '/../View/primeiroAcesso.php';
elseif ($pagina === 'principal' && $usuarioLogado) {
    $idUsuario = (int)$usuarioLogado->getID();
    $formacoes = (new FormacaoAcad())->listarFormacoes($idUsuario);
    $experiencias = (new ExperienciaProfissional())->listarExperiencias($idUsuario);
    $outrasFormacoes = (new OutrasFormacoes())->listarOutrasFormacoes($idUsuario);
    require __DIR__ . '/../View/principal.php';
} else require __DIR__ . '/../View/login.php';
