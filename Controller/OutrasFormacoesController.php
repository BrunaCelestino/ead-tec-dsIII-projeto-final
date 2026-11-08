<?php
declare(strict_types=1);

require_once __DIR__ . '/../Model/ConexaoBD.php';
require_once __DIR__ . '/../Model/OutrasFormacoes.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

/**
 * Controller responsável por receber as ações da View,
 * validar os dados e encaminhar as operações ao Model.
 */
class OutrasFormacoesController
{
    private ?int $idUsuario;

    public function __construct(?int $idUsuario)
    {
        $this->idUsuario = $idUsuario;
    }

    /**
     * Insere uma formação após validar os dados recebidos pelo formulário.
     *
     * @return array{mensagem:string,tipo:string}
     */
    public function inserir(): array
    {
        $inicio = trim((string) ($_POST['txtInicioOF'] ?? ''));
        $fim = trim((string) ($_POST['txtFimOF'] ?? ''));
        $descricao = trim((string) ($_POST['txtDescEP'] ?? ''));

        $dataInicio = DateTime::createFromFormat('!Y-m-d', $inicio);
        $dataFim = DateTime::createFromFormat('!Y-m-d', $fim);

        if (
            !$dataInicio || $dataInicio->format('Y-m-d') !== $inicio ||
            !$dataFim || $dataFim->format('Y-m-d') !== $fim
        ) {
            return [
                'mensagem' => 'Informe as datas de início e fim corretamente.',
                'tipo' => 'erro',
            ];
        }

        if ($dataFim < $dataInicio) {
            return [
                'mensagem' => 'A data final não pode ser anterior à data inicial.',
                'tipo' => 'erro',
            ];
        }

        $tamanhoDescricao = function_exists('mb_strlen')
            ? mb_strlen($descricao, 'UTF-8')
            : strlen($descricao);

        if ($descricao === '' || $tamanhoDescricao > 150) {
            return [
                'mensagem' => 'Informe uma descrição com até 150 caracteres.',
                'tipo' => 'erro',
            ];
        }

        if ($this->idUsuario === null) {
            return [
                'mensagem' => 'Cadastre ao menos um usuário no banco de dados antes de incluir outras formações.',
                'tipo' => 'erro',
            ];
        }

        $formacao = new OutrasFormacoes();
        $formacao->setIDUsuario($this->idUsuario);
        $formacao->setInicio($inicio);
        $formacao->setFim($fim);
        $formacao->setDescricao($descricao);

        if ($formacao->inserirBD()) {
            return [
                'mensagem' => 'Formação adicionada com sucesso.',
                'tipo' => 'sucesso',
            ];
        }

        return [
            'mensagem' => 'Não foi possível adicionar a formação. Verifique a conexão com o banco de dados.',
            'tipo' => 'erro',
        ];
    }

    /**
     * Remove a formação identificada pelo ID, respeitando o usuário associado.
     *
     * @return array{mensagem:string,tipo:string}
     */
    public function remover(): array
    {
        if ($this->idUsuario === null) {
            return [
                'mensagem' => 'Cadastre ao menos um usuário no banco de dados antes de remover formações.',
                'tipo' => 'erro',
            ];
        }

        $id = filter_var(
            $_POST['idoutrasformacoes'] ?? null,
            FILTER_VALIDATE_INT
        );

        if (!$id || $id < 1) {
            return [
                'mensagem' => 'Registro inválido.',
                'tipo' => 'erro',
            ];
        }

        $formacao = new OutrasFormacoes();

        if ($formacao->excluirDoUsuario((int) $id, $this->idUsuario)) {
            return [
                'mensagem' => 'Formação removida.',
                'tipo' => 'sucesso',
            ];
        }

        return [
            'mensagem' => 'Não foi possível remover a formação. O registro pode não existir para este usuário.',
            'tipo' => 'erro',
        ];
    }

    /**
     * Consulta no Model as formações vinculadas ao usuário atual.
     *
     * @return array<int, array<string, mixed>>
     */
    public function gerarLista(): array
    {
        if ($this->idUsuario === null) {
            return [];
        }

        $model = new OutrasFormacoes();
        return $model->listarOutrasFormacoes($this->idUsuario);
    }
}

/**
 * Retorna o primeiro usuário cadastrado para permitir a execução
 * independente deste módulo demonstrativo.
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
    header('Location: outras-formacoes.php', true, 303);
    exit;
}

$idUsuario = obterUsuarioPadrao();
$controller = new OutrasFormacoesController($idUsuario);

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

    $acao = (string) ($_POST['acao'] ?? '');

    if ($acao === 'adicionar') {
        $resultado = $controller->inserir();
    } elseif ($acao === 'apagar') {
        $resultado = $controller->remover();
    } else {
        $resultado = [
            'mensagem' => 'Ação não reconhecida.',
            'tipo' => 'erro',
        ];
    }

    $_SESSION['flash_outras_formacoes'] = $resultado;
    redirecionarParaModulo();
}

$registros = [];
try {
    $registros = $controller->gerarLista();
} catch (Throwable $e) {
    $mensagem = 'Não foi possível consultar as formações. Verifique a configuração do banco de dados.';
    $tipoMensagem = 'erro';
}

require __DIR__ . '/../View/outras-formacoes/index.php';
