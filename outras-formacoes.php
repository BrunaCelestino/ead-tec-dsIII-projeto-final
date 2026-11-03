<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . '/Model/OutrasFormacoes.php';

$idUsuario = filter_var($_SESSION['idusuario'] ?? null, FILTER_VALIDATE_INT);
if ($idUsuario === false || $idUsuario === null || $idUsuario <= 0) {
    http_response_code(401);
    ?>
    <!doctype html>
    <html lang="pt-BR">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Outras Formações | Acesso necessário</title>
        <style>
            body{font:16px Arial,sans-serif;background:#f3f5f7;color:#243447;display:grid;place-items:center;min-height:100vh;margin:0;padding:24px;box-sizing:border-box}
            main{max-width:560px;background:#fff;border-radius:12px;padding:28px;box-shadow:0 8px 30px #15324b18}
            h1{color:#159edb;font-size:24px;margin-top:0}p{line-height:1.55}
            code{background:#eef4f8;padding:3px 6px;border-radius:4px}
        </style>
    </head>
    <body><main>
        <h1>Outras Formações</h1>
        <p>Para acessar esta página, entre no sistema pela tela de login. A identificação do usuário deve ser armazenada na sessão como <code>idusuario</code> após a autenticação.</p>
        <p>No login, após validar a senha, utilize: <code>$_SESSION['idusuario'] = $usuario-&gt;getID();</code></p>
    </main></body>
    </html>
    <?php
    exit;
}

if (empty($_SESSION['csrf_outras_formacoes'])) {
    $_SESSION['csrf_outras_formacoes'] = bin2hex(random_bytes(32));
}

function escapar(?string $valor): string
{
    return htmlspecialchars($valor ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function dataValida(string $data): bool
{
    $objeto = DateTime::createFromFormat('!Y-m-d', $data);
    return $objeto !== false && $objeto->format('Y-m-d') === $data;
}

function dataBrasileira(?string $data): string
{
    if ($data === null || $data === '') {
        return '—';
    }
    $objeto = DateTime::createFromFormat('!Y-m-d', $data);
    return $objeto ? $objeto->format('d/m/Y') : '—';
}

$mensagem = null;
$tipoMensagem = 'sucesso';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = (string)($_POST['csrf_token'] ?? '');
    if (!hash_equals((string)$_SESSION['csrf_outras_formacoes'], $token)) {
        http_response_code(403);
        exit('Solicitação inválida. Atualize a página e tente novamente.');
    }

    $acao = (string)($_POST['acao'] ?? '');
    $modelo = new OutrasFormacoes();
    $modelo->setIDUsuario((int)$idUsuario);

    if ($acao === 'adicionar') {
        $inicio = trim((string)($_POST['inicio'] ?? ''));
        $fim = trim((string)($_POST['fim'] ?? ''));
        $descricao = trim((string)($_POST['descricao'] ?? ''));

        if ($descricao === '') {
            $mensagem = 'Informe a descrição da formação.';
            $tipoMensagem = 'erro';
        } elseif (mb_strlen($descricao, 'UTF-8') > 150) {
            $mensagem = 'A descrição deve ter no máximo 150 caracteres.';
            $tipoMensagem = 'erro';
        } elseif (($inicio !== '' && !dataValida($inicio)) || ($fim !== '' && !dataValida($fim))) {
            $mensagem = 'Informe datas válidas.';
            $tipoMensagem = 'erro';
        } elseif ($inicio !== '' && $fim !== '' && $fim < $inicio) {
            $mensagem = 'A data final não pode ser anterior à data inicial.';
            $tipoMensagem = 'erro';
        } else {
            $modelo->setInicio($inicio === '' ? null : $inicio);
            $modelo->setFim($fim === '' ? null : $fim);
            $modelo->setDescricao($descricao);

            if ($modelo->inserirBD()) {
                $_SESSION['flash_outras_formacoes'] = ['tipo' => 'sucesso', 'texto' => 'Formação adicionada com sucesso.'];
                header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
                exit;
            }
            $mensagem = 'Não foi possível salvar a formação. Verifique a conexão com o banco de dados.';
            $tipoMensagem = 'erro';
        }
    } elseif ($acao === 'excluir') {
        $idFormacao = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
        if ($idFormacao === false || $idFormacao === null || $idFormacao <= 0) {
            $mensagem = 'Formação inválida.';
            $tipoMensagem = 'erro';
        } elseif ($modelo->excluirBD((int)$idFormacao)) {
            $_SESSION['flash_outras_formacoes'] = ['tipo' => 'sucesso', 'texto' => 'Formação excluída com sucesso.'];
            header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
            exit;
        } else {
            $mensagem = 'Não foi possível excluir a formação. Ela pode não existir ou não pertencer ao usuário autenticado.';
            $tipoMensagem = 'erro';
        }
    } else {
        $mensagem = 'Ação não reconhecida.';
        $tipoMensagem = 'erro';
    }
}

if (isset($_SESSION['flash_outras_formacoes'])) {
    $flash = $_SESSION['flash_outras_formacoes'];
    $mensagem = (string)($flash['texto'] ?? '');
    $tipoMensagem = (string)($flash['tipo'] ?? 'sucesso');
    unset($_SESSION['flash_outras_formacoes']);
}

$modelo = new OutrasFormacoes();
$registros = $modelo->listarOutrasFormacoes((int)$idUsuario);
if ($modelo->getUltimoErro() !== null && $mensagem === null) {
    $mensagem = 'Não foi possível carregar as formações. Verifique a conexão com o banco de dados.';
    $tipoMensagem = 'erro';
}
$tokenCsrf = (string)$_SESSION['csrf_outras_formacoes'];
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Outras Formações</title>
    <style>
        :root{font-family:Arial,Helvetica,sans-serif;color:#263746;background:#f5f7f9;font-synthesis:none}
        *{box-sizing:border-box}
        body{margin:0;padding:32px 18px}
        .container{max-width:900px;margin:0 auto;background:#fff;padding:30px 34px;border-radius:12px;box-shadow:0 8px 28px rgba(27,55,75,.09)}
        h1{font-size:25px;font-weight:600;color:#1ba9d5;margin:0 0 7px}
        .subtitulo{margin:0 0 26px;color:#71808d;font-size:14px}
        .formulario{background:#f1f3f5;border-radius:8px;padding:22px;margin-bottom:28px}
        .campos-datas{display:grid;grid-template-columns:1fr 1fr;gap:18px}
        label{display:block;color:#637586;font-size:13px;margin-bottom:8px}
        input{width:100%;height:42px;border:1px solid #d8dfe5;border-radius:6px;background:#fff;padding:8px 11px;color:#34495a;font:14px Arial,sans-serif}
        input:focus{outline:2px solid #a9def3;border-color:#21a8e0}
        .campo-descricao{margin-top:16px}
        .acoes-form{display:flex;justify-content:center;margin-top:18px}
        button{font:inherit;cursor:pointer}
        .botao-adicionar{border:0;border-radius:6px;background:#209fe8;color:#fff;font-weight:600;padding:11px 28px;min-width:150px;box-shadow:0 2px 5px #00000012}
        .botao-adicionar:hover{background:#118bd1}
        .cabecalho-tabela{font-size:17px;color:#34495a;margin:0 0 12px}
        table{width:100%;border-collapse:collapse;font-size:14px}
        thead{background:#209fe8;color:#fff}
        th{padding:12px 14px;text-align:left;font-weight:600}
        td{padding:13px 14px;border-bottom:1px solid #e8edf1;vertical-align:middle}
        tbody tr:hover{background:#f8fbfd}
        .col-data{width:20%;white-space:nowrap}
        .col-apagar{width:100px;text-align:center}
        .botao-apagar{border:0;background:#fff0f0;color:#c43f48;border-radius:5px;padding:7px 10px;font-size:13px}
        .botao-apagar:hover{background:#ffdddd}
        .vazio{text-align:center;color:#7b8994;padding:25px}
        .alerta{padding:12px 15px;border-radius:6px;margin:0 0 18px;font-size:14px}
        .alerta.sucesso{background:#e9f7ef;color:#236a43}
        .alerta.erro{background:#fff0f0;color:#a52e38}
        .rodape{margin-top:18px;color:#89959e;font-size:12px;text-align:right}
        @media(max-width:600px){
            body{padding:12px}
            .container{padding:22px 16px}
            .campos-datas{grid-template-columns:1fr;gap:14px}
            .formulario{padding:16px}
            table{font-size:12px}
            th,td{padding:10px 7px}
            .col-data{width:auto}
        }
    </style>
</head>
<body>
<main class="container">
    <h1>Outras Formações</h1>
    <p class="subtitulo">Cadastre e consulte cursos, treinamentos e outras formações complementares.</p>

    <?php if ($mensagem !== null && $mensagem !== ''): ?>
        <div class="alerta <?= escapar($tipoMensagem) ?>" role="status"><?= escapar($mensagem) ?></div>
    <?php endif; ?>

    <section class="formulario" aria-label="Cadastro de outras formações">
        <form method="post" action="">
            <input type="hidden" name="csrf_token" value="<?= escapar($tokenCsrf) ?>">
            <input type="hidden" name="acao" value="adicionar">
            <div class="campos-datas">
                <div>
                    <label for="inicio">Data inicial</label>
                    <input type="date" id="inicio" name="inicio">
                </div>
                <div>
                    <label for="fim">Data final</label>
                    <input type="date" id="fim" name="fim">
                </div>
            </div>
            <div class="campo-descricao">
                <label for="descricao">Descrição</label>
                <input type="text" id="descricao" name="descricao" maxlength="150" placeholder="Ex.: Curso de Inglês - Inglês City" required>
            </div>
            <div class="acoes-form">
                <button class="botao-adicionar" type="submit">＋ Adicionar</button>
            </div>
        </form>
    </section>

    <h2 class="cabecalho-tabela">Formações cadastradas</h2>
    <div style="overflow-x:auto">
        <table>
            <thead>
                <tr>
                    <th class="col-data">Início</th>
                    <th class="col-data">Fim</th>
                    <th>Descrição</th>
                    <th class="col-apagar">Apagar</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($registros === []): ?>
                <tr><td class="vazio" colspan="4">Nenhuma outra formação cadastrada.</td></tr>
            <?php else: ?>
                <?php foreach ($registros as $registro): ?>
                    <tr>
                        <td><?= escapar(dataBrasileira($registro['inicio'] ?? null)) ?></td>
                        <td><?= escapar(dataBrasileira($registro['fim'] ?? null)) ?></td>
                        <td><?= escapar((string)($registro['descricao'] ?? '')) ?></td>
                        <td class="col-apagar">
                            <form method="post" action="" onsubmit="return confirm('Deseja realmente apagar esta formação?');">
                                <input type="hidden" name="csrf_token" value="<?= escapar($tokenCsrf) ?>">
                                <input type="hidden" name="acao" value="excluir">
                                <input type="hidden" name="id" value="<?= (int)$registro['idoutrasformacoes'] ?>">
                                <button class="botao-apagar" type="submit" aria-label="Apagar formação">Apagar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <p class="rodape"><?= count($registros) ?> formação(ões) cadastrada(s)</p>
</main>
</body>
</html>
