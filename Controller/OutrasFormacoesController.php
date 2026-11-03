<?php
declare(strict_types=1);

require_once __DIR__ . '/../Model/ConexaoBD.php';
require_once __DIR__ . '/../Model/OutrasFormacoes.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function obterUsuarioPadrao(): ?int
{
    $con = (new ConexaoBD())->conectar();
    $resultado = $con->query('SELECT idusuario FROM usuario ORDER BY idusuario LIMIT 1');
    $linha = $resultado->fetch_assoc();
    $con->close();

    return $linha ? (int) $linha['idusuario'] : null;
}

// O projeto ainda não possui a tela principal integrada nem contexto de usuário.
// Para permitir testar a atividade sem criar login, usa-se o primeiro usuário cadastrado.
// Na integração com a tela principal, este ID deverá vir do usuário já selecionado pelo sistema.
$idUsuario = obterUsuarioPadrao();

$mensagem = '';
$tipoMensagem = '';

// Token simples para proteger as operações enviadas por formulário.
if (empty($_SESSION['csrf_outras_formacoes'])) {
    $_SESSION['csrf_outras_formacoes'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = (string) ($_POST['csrf_token'] ?? '');

    if (!hash_equals($_SESSION['csrf_outras_formacoes'], $token)) {
        $mensagem = 'Não foi possível validar a solicitação. Atualize a página e tente novamente.';
        $tipoMensagem = 'erro';
    } elseif ($idUsuario === null) {
        $mensagem = 'Cadastre ao menos um usuário no banco de dados antes de incluir outras formações.';
        $tipoMensagem = 'erro';
    } else {
        $acao = (string) ($_POST['acao'] ?? '');

        if ($acao === 'adicionar') {
            $inicio = trim((string) ($_POST['txtInicioOF'] ?? ''));
            $fim = trim((string) ($_POST['txtFimOF'] ?? ''));
            $descricao = trim((string) ($_POST['txtDescEP'] ?? ''));

            $dataInicio = DateTime::createFromFormat('!Y-m-d', $inicio);
            $dataFim = DateTime::createFromFormat('!Y-m-d', $fim);

            if (!$dataInicio || $dataInicio->format('Y-m-d') !== $inicio ||
                !$dataFim || $dataFim->format('Y-m-d') !== $fim) {
                $mensagem = 'Informe as datas de início e fim corretamente.';
                $tipoMensagem = 'erro';
            } elseif ($dataFim < $dataInicio) {
                $mensagem = 'A data final não pode ser anterior à data inicial.';
                $tipoMensagem = 'erro';
            } elseif ($descricao === '' || mb_strlen($descricao) > 150) {
                $mensagem = 'Informe uma descrição com até 150 caracteres.';
                $tipoMensagem = 'erro';
            } else {
                $formacao = new OutrasFormacoes();
                $formacao->setIDUsuario($idUsuario);
                $formacao->setInicio($inicio);
                $formacao->setFim($fim);
                $formacao->setDescricao($descricao);

                if ($formacao->inserirBD()) {
                    $mensagem = 'Formação adicionada com sucesso.';
                    $tipoMensagem = 'sucesso';
                } else {
                    $mensagem = 'Não foi possível adicionar a formação. Verifique a conexão com o banco de dados.';
                    $tipoMensagem = 'erro';
                }
            }
        } elseif ($acao === 'apagar') {
            $id = filter_input(INPUT_POST, 'idoutrasformacoes', FILTER_VALIDATE_INT);

            if (!$id) {
                $mensagem = 'Registro inválido.';
                $tipoMensagem = 'erro';
            } else {
                $formacao = new OutrasFormacoes();
                if ($formacao->excluirBD((int) $id)) {
                    $mensagem = 'Formação removida.';
                    $tipoMensagem = 'sucesso';
                } else {
                    $mensagem = 'Não foi possível remover a formação.';
                    $tipoMensagem = 'erro';
                }
            }
        }
    }
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
