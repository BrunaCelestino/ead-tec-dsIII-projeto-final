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
            padding: 10px;
            background: #fff;
            color: #333;
            font-family: Arial, Helvetica, sans-serif;
        }
        .painel {
            width: min(100%, 820px);
            margin: 0 auto;
            padding: 10px 28px 30px;
            background: #f0f0f0;
        }
        h1 {
            margin: 0 0 24px;
            color: #45bfd0;
            font-size: 30px;
            font-weight: 400;
        }
        .linha-datas {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
            margin-bottom: 14px;
        }
        .campo { margin-bottom: 12px; }
        label {
            display: block;
            margin: 0 0 30px;
            color: #65a9df;
            font-size: 14px;
            font-weight: 600;
            text-align: center;
        }
        .grupo-campo { display: flex; align-items: center; gap: 12px; }
        .icone { width: 40px; height: 40px; flex: 0 0 40px; color: #2499ed; }
        input[type="date"], input[type="text"] {
            width: 100%;
            min-width: 0;
            height: 44px;
            padding: 8px 12px;
            border: 1px solid #d8d8d8;
            border-radius: 9px;
            background: #fff;
            color: #555;
            font-size: 14px;
            box-shadow: 0 1px 2px rgba(0,0,0,.08);
        }
        input:focus { outline: 1px solid #2499ed; }
        .descricao label { display: none; }
        .descricao { margin-top: 3px; }
        .botao-adicionar {
            display: flex;
            width: 144px;
            height: 58px;
            align-items: center;
            justify-content: center;
            margin: 16px auto;
            border: 0;
            border-radius: 10px;
            background: #2196ed;
            color: #fff;
            cursor: pointer;
        }
        .botao-adicionar:hover { background: #1686d8; }
        .botao-adicionar svg { width: 42px; height: 42px; }
        table {
            width: calc(100% - 34px);
            margin: 0 auto;
            border-collapse: collapse;
            background: #fff;
            font-size: 14px;
        }
        thead { background: #2196ed; color: #fff; }
        th, td {
            height: 42px;
            padding: 9px 8px;
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
            .linha-datas { gap: 10px; }
            .grupo-campo { gap: 7px; }
            .icone { width: 30px; height: 30px; flex-basis: 30px; }
            table { width: 100%; font-size: 11px; }
            th, td { padding: 7px 4px; }
        }
        @media (max-width: 430px) {
            .linha-datas { grid-template-columns: 1fr; gap: 12px; }
            label { margin-bottom: 8px; }
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
                    <label for="txtInicioOF">Data Inicial</label>
                    <div class="grupo-campo">
                        <svg class="icone" viewBox="0 0 48 48" fill="none" aria-hidden="true">
                            <rect x="6" y="10" width="36" height="32" rx="4" stroke="currentColor" stroke-width="4"/>
                            <path d="M6 19h36M16 5v10M32 5v10" stroke="currentColor" stroke-width="4" stroke-linecap="round"/>
                            <path d="M15 26h4v4h-4zM25 26h4v4h-4zM15 34h4v4h-4zM25 34h4v4h-4zM34 26h3v4h-3z" fill="currentColor"/>
                        </svg>
                        <input type="date" id="txtInicioOF" name="txtInicioOF" required>
                    </div>
                </div>
                <div class="campo">
                    <label for="txtFimOF">Data Final</label>
                    <div class="grupo-campo">
                        <svg class="icone" viewBox="0 0 48 48" fill="none" aria-hidden="true">
                            <rect x="6" y="10" width="36" height="32" rx="4" stroke="currentColor" stroke-width="4"/>
                            <path d="M6 19h36M16 5v10M32 5v10" stroke="currentColor" stroke-width="4" stroke-linecap="round"/>
                            <path d="M15 26h4v4h-4zM25 26h4v4h-4zM15 34h4v4h-4zM25 34h4v4h-4zM34 26h3v4h-3z" fill="currentColor"/>
                        </svg>
                        <input type="date" id="txtFimOF" name="txtFimOF" required>
                    </div>
                </div>
            </div>

            <div class="campo descricao">
                <label for="txtDescEP">Descrição</label>
                <div class="grupo-campo">
                    <svg class="icone" viewBox="0 0 48 48" fill="none" aria-hidden="true">
                        <path d="M7 10h34M7 20h34M7 30h34M7 40h34" stroke="currentColor" stroke-width="5" stroke-linecap="square"/>
                    </svg>
                    <input type="text" id="txtDescEP" name="txtDescEP"
                           maxlength="150" placeholder="Ex: Curso de Inglês - Inglês City" required>
                </div>
            </div>

            <button class="botao-adicionar" type="submit" name="btnAddOF" aria-label="Adicionar formação">
                <svg viewBox="0 0 48 48" fill="none" aria-hidden="true">
                    <circle cx="18" cy="13" r="8" fill="currentColor"/>
                    <path d="M3 39c0-8 6-13 15-13s15 5 15 13v3H3v-3z" fill="currentColor"/>
                    <path d="M37 19v18M28 28h18" stroke="currentColor" stroke-width="5" stroke-linecap="round"/>
                </svg>
            </button>
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
