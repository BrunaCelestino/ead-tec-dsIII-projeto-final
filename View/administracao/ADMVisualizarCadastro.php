<?php
declare(strict_types=1);

function admEsc($valor): string
{
    return htmlspecialchars((string)($valor ?? ''), ENT_QUOTES, 'UTF-8');
}

function admDataBR($valor): string
{
    if (empty($valor)) return '';
    $data = DateTime::createFromFormat('Y-m-d', (string)$valor);
    return $data ? $data->format('d/m/Y') : admEsc($valor);
}

function admTabela(array $registros, string $tipo): void
{
    $colunas = $tipo === 'experiencia'
        ? ['Início', 'Fim', 'Empresa', 'Descrição']
        : ['Início', 'Fim', 'Descrição'];

    echo '<div class="tabela-responsiva"><table><thead><tr>';
    foreach ($colunas as $coluna) echo '<th>' . admEsc($coluna) . '</th>';
    echo '</tr></thead><tbody>';

    if (!$registros) {
        echo '<tr><td class="vazio" colspan="' . count($colunas) . '">Nenhum registro cadastrado.</td></tr>';
    } else {
        foreach ($registros as $registro) {
            echo '<tr>';
            echo '<td>' . admDataBR($registro['inicio'] ?? '') . '</td>';
            echo '<td>' . admDataBR($registro['fim'] ?? '') . '</td>';
            if ($tipo === 'experiencia') echo '<td>' . admEsc($registro['empresa'] ?? '') . '</td>';
            echo '<td>' . admEsc($registro['descricao'] ?? '') . '</td>';
            echo '</tr>';
        }
    }
    echo '</tbody></table></div>';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Visualizar cadastro | Área administrativa</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <style>
        * { box-sizing:border-box; }
        body { margin:0; padding:28px 16px; background:#fff; color:#45515c; font-family:Arial,Helvetica,sans-serif; }
        .pagina { width:min(900px,100%); margin:0 auto; }
        .titulo-principal { margin:0 0 28px; padding:9px 14px; border-radius:5px; background:#08b9c9; color:#fff; text-align:center; font-size:20px; font-weight:400; }
        .dados { width:min(560px,100%); margin:0 auto 32px; }
        .dado { margin:0 0 8px; padding:8px 12px; border-radius:5px; background:#08b9c9; color:#fff; overflow-wrap:anywhere; }
        .secao { margin:28px 0; }
        .secao h2 { margin:0 0 12px; color:#08b9c9; text-align:center; font-size:20px; font-weight:400; }
        .tabela-responsiva { overflow-x:auto; }
        table { width:100%; border-collapse:collapse; border:1px solid #d9d9d9; font-size:13px; }
        th { padding:9px 10px; border:1px solid #d9d9d9; background:#2196e6; color:#fff; text-align:center; }
        td { padding:9px 10px; border:1px solid #d9d9d9; background:#fff; text-align:center; }
        tbody tr:nth-child(even) td { background:#f1f1f1; }
        .vazio { color:#777; padding:16px; }
        .acoes { margin:30px 0 10px; text-align:center; }
        .voltar { display:inline-block; padding:10px 22px; border-radius:5px; background:#2196e6; color:#fff; text-decoration:none; }
        .voltar:hover { background:#167fc4; color:#fff; }
        @media(max-width:600px) { body { padding:16px 10px; } .titulo-principal { font-size:18px; } }
    </style>
</head>
<body>
<main class="pagina">
    <h1 class="titulo-principal"><?= admEsc($usuarioVisualizado->getNome()) ?> - Currículo</h1>

    <section class="dados" aria-label="Dados pessoais">
        <p class="dado"><strong>NOME:</strong> <?= admEsc($usuarioVisualizado->getNome()) ?></p>
        <p class="dado"><strong>CPF:</strong> <?= admEsc($usuarioVisualizado->getCPF()) ?></p>
        <p class="dado"><strong>E-MAIL:</strong> <?= admEsc($usuarioVisualizado->getEmail()) ?></p>
        <p class="dado"><strong>DATA DE NASCIMENTO:</strong> <?= admDataBR($usuarioVisualizado->getDataNascimento()) ?></p>
    </section>

    <section class="secao">
        <h2>Formação Acadêmica</h2>
        <?php admTabela($formacoesVisualizadas, 'formacao'); ?>
    </section>

    <section class="secao">
        <h2>Experiência Profissional</h2>
        <?php admTabela($experienciasVisualizadas, 'experiencia'); ?>
    </section>

    <section class="secao">
        <h2>Outras Formações</h2>
        <?php admTabela($outrasFormacoesVisualizadas, 'outras'); ?>
    </section>

    <div class="acoes">
        <a class="voltar" href="index.php?area=admin&amp;pagina=usuarios">Voltar à lista de usuários</a>
    </div>
</main>
</body>
</html>
