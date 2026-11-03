<?php
declare(strict_types=1);

require_once __DIR__ . '/../Model/ConexaoBD.php';
require_once __DIR__ . '/../Model/OutrasFormacoes.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

/**
 * Retorna o usuário de referência enquanto o sistema principal não fornece
 * um contexto de usuário. Na integração, substituir pela identificação
 * já utilizada pela aplicação, sem criar um fluxo de login novo.
 */
function obterUsuarioPadrao(): ?int
{
    $con = (new ConexaoBD())->conectar();
    $resultado = $con->query('SELECT idusuario FROM usuario ORDER BY idusuario LIMIT 1');
    $linha = $resultado->fetch_assoc();
    $con->close();

    return $linha ? (int) $linha['idusuario'] : null;
}

function redirecionarParaModulo(): never
{
    header('Location: ../outras-formacoes.php', true, 303);
    exit;
}

$idUsuario = obterUsuarioPadrao();

// Mensagens armazenadas na sessão para sobreviver ao redirecionamento POST/GET.
$mensagem = (string) ($_SESSION['flash_outras_formacoes']['mensagem'] ?? '');
$tipoMensagem = (string) ($_SESSION['flash_outras_formacoes']['tipo'] ?? '');
unset($_SESSION['flash_outras_formacoes']);

if (empty($_SESSION['csrf_outras_formacoes'])) {
    $_SESSION['csrf_outras_formacoes'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = (string) ($_POST['csrf_token'] ?? '');

    if (!hash_equals($_SESSION['csrf_outras_formacoes'], $token)) {
        $_SESSION['flash_outras_formacoes'] = [
            'mensagem' => 'Não foi possível validar a solicitação. Atualize a página e tente novamente.',
            'tipo' => 'erro',
        ];
        redirecionarParaModulo();
    }

    if ($idUsuario === null) {
        $_SESSION['flash_outras_formacoes'] = [
            'mensagem' => 'Cadastre ao menos um usuário no banco de dados antes de incluir outras formações.',
            'tipo' => 'erro',
        ];
        redirecionarParaModulo();
    }

    $acao = (string) ($_POST['acao'] ?? '');

    if ($acao === 'adicionar') {
        $inicio = trim((string) ($_POST['txtInicioOF'] ?? ''));
        $fim = trim((string) ($_POST['txtFimOF'] ?? ''));
        $descricao = trim((string) ($_POST['txtDescEP'] ?? ''));

        $dataInicio = DateTime::createFromFormat('!Y-m-d', $inicio);
        $dataFim = DateTime::createFromFormat('!Y-m-d', $fim);

        if (!$dataInicio || $dataInicio->format('Y-m-d') !== $inicio ||
            !$dataFim || $dataFim->format('Y-m-d') !== $fim) {
            $mensagemPost = 'Informe as datas de início e fim corretamente.';
            $tipoPost = 'erro';
        } elseif ($dataFim < $dataInicio) {
            $mensagemPost = 'A data final não pode ser anterior à data inicial.';
            $tipoPost = 'erro';
        } elseif ($descricao === '' || (function_exists('mb_strlen') ? mb_strlen($descricao, 'UTF-8') : strlen($descricao)) > 150) {
            $mensagemPost = 'Informe uma descrição com até 150 caracteres.';
            $tipoPost = 'erro';
        } else {
            $formacao = new OutrasFormacoes();
            $formacao->setIDUsuario($idUsuario);
            $formacao->setInicio($inicio);
            $formacao->setFim($fim);
            $formacao->setDescricao($descricao);

            if ($formacao->inserirBD()) {
                $mensagemPost = 'Formação adicionada com sucesso.';
                $tipoPost = 'sucesso';
            } else {
                $mensagemPost = 'Não foi possível adicionar a formação. Verifique a conexão com o banco de dados.';
                $tipoPost = 'erro';
            }
        }
    } elseif ($acao === 'apagar') {
        $id = filter_input(INPUT_POST, 'idoutrasformacoes', FILTER_VALIDATE_INT);

        if (!$id) {
            $mensagemPost = 'Registro inválido.';
            $tipoPost = 'erro';
        } else {
            $formacao = new OutrasFormacoes();
            if ($formacao->excluirDoUsuario((int) $id, $idUsuario)) {
                $mensagemPost = 'Formação removida.';
                $tipoPost = 'sucesso';
            } else {
                $mensagemPost = 'Não foi possível remover a formação. O registro pode não existir para este usuário.';
                $tipoPost = 'erro';
            }
        }
    } else {
        $mensagemPost = 'Ação não reconhecida.';
        $tipoPost = 'erro';
    }

    $_SESSION['flash_outras_formacoes'] = [
        'mensagem' => $mensagemPost,
        'tipo' => $tipoPost,
    ];
    redirecionarParaModulo();
}

$registros = [];
if ($idUsuario !== null) {
    try {
        $model = new OutrasFormacoes();
        $registros = $model->listarOutrasFormacoes($idUsuario);
    } catch (Throwable $e) {
        $mensagem = 'Não foi possível consultar as formações. Verifique a configuração do banco de dados.';
        $tipoMensagem = 'erro';
    }
}

require __DIR__ . '/../View/outras-formacoes/index.php';
