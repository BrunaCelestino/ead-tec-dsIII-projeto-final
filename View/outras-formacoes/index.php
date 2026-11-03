<?php
declare(strict_types=1);

function escapar(?string $valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

function formatarData(?string $data): string
{
    if (!$data) {
        return '';
    }
    $dataObj = DateTime::createFromFormat('Y-m-d', $data);
    return $dataObj ? $dataObj->format('d/m/Y') : escapar($data);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Outras Formações</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 24px;
            background: #f5f5f5;
            color: #333;
            font-family: Arial, Helvetica, sans-serif;
        }
        .painel {
            width: min(100%, 760px);
            margin: 0 auto;
            padding: 18px 22px 24px;
            background: #f0f0f0;
        }
        h1 {
            margin: 0 0 18px;
            color: #16b9d0;
            font-size: 24px;
            font-weight: 400;
        }
        .linha-datas {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }
        .campo { margin-bottom: 12px; }
        label {
            display: block;
            margin: 0 0 7px;
            color: #168ed1;
            font-size: 12px;
            text-align: center;
        }
        input[type="date"], input[type="text"] {
            width: 100%;
            min-height: 38px;
            padding: 8px 10px;
            border: 1px solid #d7d7d7;
            border-radius: 5px;
            background: #fff;
            color: #444;
            font-size: 13px;
        }
        .descricao { margin-top: 3px; }
        .botao-adicionar {
            display: block;
            min-width: 100px;
            margin: 15px auto 18px;
            padding: 9px 22px;
            border: 0;
            border-radius: 5px;
            background: #168fe0;
            color: #fff;
            font-size: 14px;
            cursor: pointer;
        }
        .botao-adicionar:hover { background: #087ac7; }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            font-size: 13px;
        }
        thead { background: #168fe0; color: #fff; }
        th, td {
            padding: 10px 8px;
            border: 1px solid #e1e1e1;
            text-align: center;
        }
        tbody tr:nth-child(even) { background: #f8f8f8; }
        .botao-apagar {
            padding: 5px 10px;
            border: 0;
            border-radius: 3px;
            background: #d9534f;
            color: #fff;
            cursor: pointer;
        }
        .mensagem {
            margin: 0 0 14px;
            padding: 10px;
            border-radius: 4px;
            font-size: 13px;
        }
        .mensagem.sucesso { background: #e2f3e6; color: #246b35; }
        .mensagem.erro { background: #fbe7e7; color: #922; }
        .vazio { padding: 18px; color: #777; text-align: center; }
        @media (max-width: 560px) {
            body { padding: 10px; }
            .painel { padding: 15px 12px; }
            .linha-datas { grid-template-columns: 1fr; gap: 0; }
            table { font-size: 11px; }
            th, td { padding: 7px 4px; }
        }
    </style>
</head>
<body>
<main class="painel">
    <h1>Outras Formações</h1>

    <?php if ($mensagem !== ''): ?>
        <div class="mensagem <?= escapar($tipoMensagem) ?>" role="status">
            <?= escapar($mensagem) ?>
        </div>
    <?php endif; ?>

    <?php if ($idUsuario === null): ?>
        <p>Não há usuário cadastrado no banco de dados. Cadastre um usuário para associar as formações.</p>
    <?php else: ?>
        <form method="post" action="">
            <input type="hidden" name="csrf_token" value="<?= escapar($_SESSION['csrf_outras_formacoes']) ?>">
            <input type="hidden" name="acao" value="adicionar">

            <div class="linha-datas">
                <div class="campo">
                    <label for="txtInicioOF">Data inicial</label>
                    <input type="date" id="txtInicioOF" name="txtInicioOF" required>
                </div>
                <div class="campo">
                    <label for="txtFimOF">Data final</label>
                    <input type="date" id="txtFimOF" name="txtFimOF" required>
                </div>
            </div>

            <div class="campo descricao">
                <label for="txtDescEP">Descrição</label>
                <input type="text" id="txtDescEP" name="txtDescEP"
                       maxlength="150" placeholder="Ex: Curso de Inglês - Inglês City" required>
            </div>

            <button class="botao-adicionar" type="submit" name="btnAddOF">Adicionar</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Início</th>
                    <th>Fim</th>
                    <th>Descrição</th>
                    <th>Apagar</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!$registros): ?>
                <tr><td class="vazio" colspan="4">Nenhuma formação cadastrada.</td></tr>
            <?php else: ?>
                <?php foreach ($registros as $registro): ?>
                    <tr>
                        <td><?= escapar(formatarData($registro['inicio'] ?? null)) ?></td>
                        <td><?= escapar(formatarData($registro['fim'] ?? null)) ?></td>
                        <td><?= escapar($registro['descricao'] ?? '') ?></td>
                        <td>
                            <form method="post" action="" onsubmit="return confirm('Deseja apagar esta formação?');">
                                <input type="hidden" name="csrf_token" value="<?= escapar($_SESSION['csrf_outras_formacoes']) ?>">
                                <input type="hidden" name="acao" value="apagar">
                                <input type="hidden" name="idoutrasformacoes" value="<?= (int) $registro['idoutrasformacoes'] ?>">
                                <button class="botao-apagar" type="submit">Apagar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    <?php endif; ?>
</main>
</body>
</html>
