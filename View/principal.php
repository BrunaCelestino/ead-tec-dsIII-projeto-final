<?php
declare(strict_types=1);

function h($valor): string
{
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}

function dataBR($valor): string
{
    if (!$valor) return '';
    $data = DateTime::createFromFormat('Y-m-d', (string)$valor);
    return $data ? $data->format('d/m/Y') : h($valor);
}

function tabelaRegistros(array $registros, string $tipo, array $colunas, string $campoId): void
{
    $acao = $tipo === 'experiencia' ? 'experiencia' : ($tipo === 'outras' ? 'outra-formacao' : 'formacao');
    echo '<div class="tabela-container"><table class="tabela-registros"><thead><tr>';
    foreach ($colunas as $coluna) echo '<th>' . h($coluna) . '</th>';
    echo '<th class="coluna-acao">Ações</th></tr></thead><tbody>';

    if (!$registros) {
        echo '<tr><td colspan="' . (count($colunas) + 1) . '" class="sem-registros">Nenhum registro cadastrado.</td></tr>';
    }

    foreach ($registros as $registro) {
        echo '<tr>';
        echo '<td>' . h(dataBR($registro['inicio'] ?? '')) . '</td>';
        echo '<td>' . h(dataBR($registro['fim'] ?? '')) . '</td>';
        if ($tipo === 'experiencia') echo '<td>' . h($registro['empresa'] ?? '') . '</td>';
        echo '<td>' . h($registro['descricao'] ?? '') . '</td>';
        echo '<td><form method="post" action="index.php?pagina=principal" onsubmit="return confirm(\'Deseja excluir este registro?\')">';
        echo '<input type="hidden" name="csrf_token" value="' . h($GLOBALS['csrf']) . '">';
        echo '<input type="hidden" name="idRegistro" value="' . (int)$registro[$campoId] . '">';
        echo '<input type="hidden" name="acao" value="excluir-' . $acao . '">';
        echo '<button class="botao-excluir" type="submit" aria-label="Excluir registro"><i class="fa fa-trash"></i> Excluir</button>';
        echo '</form></td></tr>';
    }
    echo '</tbody></table></div>';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Meu currículo | Sistema de Currículos</title>
    <style>
        :root { --azul:#2196e6; --azul-escuro:#16b5c9; --azul-claro:#e8f7fb; --fundo:#ffffff; --texto:#45515c; --cartao:#f1f1f1; }
        * { box-sizing:border-box; }
        html { scroll-behavior:smooth; scroll-padding-top:24px; }
        body { margin:0; background:#fff; color:var(--texto); font-family:Arial,Helvetica,sans-serif; }
        .menu-lateral { position:fixed; inset:0 auto 0 0; width:250px; background:var(--azul); color:#fff; padding:26px 16px; display:flex; flex-direction:column; z-index:10; box-shadow:2px 0 12px #102b461c; }
        .marca { padding:0 14px 28px; font-size:18px; line-height:1.45; font-weight:bold; letter-spacing:.5px; }
        .menu-links { display:flex; flex-direction:column; gap:6px; }
        .menu-links a { color:#eaf2fa; text-decoration:none; padding:13px 14px; border-radius:7px; transition:background .2s; }
        .menu-links a:hover { background:#ffffff20; color:#fff; }
        .menu-links i { width:25px; }
        .menu-sair { margin-top:auto; padding:12px 8px 0; }
        .menu-sair button { width:100%; border:0; border-radius:7px; padding:12px; cursor:pointer; background:#fff; color:var(--azul); font-weight:bold; }
        .conteudo { margin-left:250px; padding:34px clamp(18px,4vw,54px); }
        .topo { max-width:1120px; margin:0 auto 26px; display:flex; justify-content:space-between; align-items:center; gap:20px; }
        .topo h1 { margin:0; font-size:28px; color:var(--azul-escuro); font-weight:700; }
        .topo p { margin:7px 0 0; color:#657487; }
        .identificacao { color:var(--azul); background:#fff; border:1px solid #e2e9f1; border-radius:30px; padding:10px 16px; white-space:nowrap; }
        .alerta { max-width:1120px; margin:0 auto 22px; padding:14px 18px; border-radius:8px; }
        .alerta.sucesso { background:#e8f5ed; color:#21633b; border:1px solid #c9e8d3; }
        .alerta.erro { background:#fff0f0; color:#9c2929; border:1px solid #f1cccc; }
        .secao { max-width:1120px; margin:0 auto 24px; padding:18px 22px; background:var(--cartao); border:0; border-radius:0; box-shadow:none; }
        .titulo-secao { display:flex; align-items:center; gap:12px; margin:0 0 20px; color:var(--azul-escuro); font-size:21px; font-weight:400; }
        .titulo-secao i { display:none; }
        .campo-com-icone { position:relative; display:flex; align-items:center; gap:8px; }
        .campo-com-icone .icone-campo { flex:0 0 22px; width:22px; height:22px; color:var(--azul); }
        .campo-com-icone .campo { min-width:0; }
        .botao-principal svg { width:22px; height:22px; vertical-align:middle; }
        label { display:block; margin:0 0 7px; color:#2196e6; font-size:12px; font-weight:400; text-align:center; }
        .campo { width:100%; min-height:38px; padding:8px 12px; margin-bottom:10px; border:1px solid #d4d4d4; border-radius:6px; background:#fff; color:var(--texto); font:inherit; font-size:13px; }
        .campo:focus { outline:2px solid #9fc4e8; border-color:var(--azul); }
        .linha-campos { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:10px; }
        .botao-principal { display:block; min-width:70px; margin:4px auto 8px; border:0; border-radius:6px; padding:10px 22px; background:var(--azul); color:#fff; font-size:19px; cursor:pointer; }
        .botao-principal:hover { background:var(--azul-escuro); }
        .botao-adicionar { width:100px; height:52px; padding:10px 22px; }
        .botao-adicionar svg { width:28px; height:28px; }
        .botao-atualizar { width:90%; min-height:36px; padding:8px 14px; font-size:13px; }
        #dados-pessoais .campo-com-icone { gap:12px; }
        #dados-pessoais .campo-com-icone .icone-campo { flex-basis:28px; width:28px; height:24px; }
        #dados-pessoais .campo { margin-bottom:8px; }
        .tabela-container { width:100%; overflow-x:auto; margin-top:22px; }
        .tabela-registros { width:100%; border-collapse:collapse; font-size:14px; }
        .tabela-registros th { padding:8px 13px; background:var(--azul); color:#fff; text-align:center; white-space:nowrap; font-size:12px; }
        .tabela-registros td { padding:8px 13px; border-bottom:1px solid #e8edf2; vertical-align:middle; background:#fff; }
        .tabela-registros tr:last-child td { border-bottom:0; }
        .coluna-acao { width:120px; }
        .sem-registros { padding:20px !important; color:#7b8794; text-align:center; }
        .botao-excluir { border:0; border-radius:5px; padding:7px 10px; color:#fff; background:var(--azul); cursor:pointer; white-space:nowrap; }
        .botao-excluir:hover { background:#167fc4; }
        footer { max-width:1120px; margin:28px auto 0; padding:18px; color:#8190a0; text-align:center; font-size:13px; }
        @media(max-width:760px) {
            .menu-lateral { position:relative; width:100%; padding:14px; }
            .marca { padding:4px 8px 12px; }
            .menu-links { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); }
            .menu-links a { padding:10px; font-size:14px; }
            .menu-sair { margin-top:12px; padding:4px 0 0; }
            .conteudo { margin:0; padding:22px 14px; }
            .topo { align-items:flex-start; flex-direction:column; }
            .topo h1 { font-size:24px; }
            .secao { padding:21px 17px; }
        }
        @media(max-width:480px) { .linha-campos { grid-template-columns:1fr; gap:0; } .menu-links { grid-template-columns:1fr 1fr; } }
    </style>
</head>
<body>
    <aside class="menu-lateral">
        <div class="marca"><i class="fa fa-file-text-o"></i> &nbsp;SISTEMA DE<br>CURRÍCULOS</div>
        <nav class="menu-links" aria-label="Navegação do currículo">
            <a href="#inicio"><i class="fa fa-home"></i> Início</a>
            <a href="#dados-pessoais"><i class="fa fa-user"></i> Dados pessoais</a>
            <a href="#formacao"><i class="fa fa-graduation-cap"></i> Formação acadêmica</a>
            <a href="#experiencia"><i class="fa fa-briefcase"></i> Experiência profissional</a>
            <a href="#outras-formacoes"><i class="fa fa-book"></i> Outras formações</a>
        </nav>
        <form class="menu-sair" method="post" action="index.php">
            <input type="hidden" name="csrf_token" value="<?= h($csrf) ?>">
            <input type="hidden" name="acao" value="sair">
            <button type="submit"><i class="fa fa-sign-out"></i> &nbsp;Sair</button>
        </form>
    </aside>

    <main class="conteudo">
        <?php if ($flash): ?>
            <div class="alerta <?= ($flash['tipo'] ?? '') === 'erro' ? 'erro' : 'sucesso' ?>" role="status">
                <?= h($flash['texto'] ?? '') ?>
            </div>
        <?php endif; ?>

        <header class="topo" id="inicio">
            <div>
                <h1>Meu currículo</h1>
                <p>Gerencie todas as informações do seu currículo em um só lugar.</p>
            </div>
            <div class="identificacao"><i class="fa fa-user-circle"></i> &nbsp;<?= h($usuarioLogado->getNome()) ?></div>
        </header>

        <section class="secao" id="dados-pessoais">
            <h2 class="titulo-secao"><i class="fa fa-user"></i> Dados pessoais</h2>
            <form method="post" action="index.php?pagina=principal">
                <input type="hidden" name="csrf_token" value="<?= h($csrf) ?>">
                <input type="hidden" name="acao" value="atualizar-dados">
                <div class="campo-com-icone"><svg class="icone-campo" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="7" r="4"/><path d="M4 21v-2a8 8 0 0 1 16 0v2z"/></svg><input class="campo" aria-label="Nome completo" id="nome" name="txtNome" value="<?= h($usuarioLogado->getNome()) ?>" placeholder="Nome completo" required maxlength="150"></div>
                <div class="campo-com-icone"><svg class="icone-campo" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="17" rx="2"/><path d="M16 2v4M8 2v4M3 9h18"/></svg><input class="campo" aria-label="Data de nascimento" id="nascimento" type="date" name="txtData" value="<?= h($usuarioLogado->getDataNascimento()) ?>"></div>
                <div class="campo-com-icone"><svg class="icone-campo" viewBox="0 0 24 24" fill="currentColor"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M6 9h5v1H6zm0 3h5v1H6zm8-2h5v4h-5z" fill="#fff"/></svg><input class="campo" aria-label="CPF" id="cpf" name="txtCPF" value="<?= h($usuarioLogado->getCPF()) ?>" placeholder="CPF" required maxlength="11" inputmode="numeric"></div>
                <div class="campo-com-icone"><svg class="icone-campo" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m3 6 9 7 9-7"/></svg><input class="campo" aria-label="E-mail" id="email" type="email" name="txtEmail" value="<?= h($usuarioLogado->getEmail()) ?>" placeholder="E-mail" maxlength="150"></div>
                <button class="botao-principal botao-atualizar" type="submit">Atualizar</button>
            </form>
        </section>

        <section class="secao" id="formacao">
            <h2 class="titulo-secao"><i class="fa fa-graduation-cap"></i> Formação acadêmica</h2>
            <form method="post" action="index.php?pagina=principal">
                <input type="hidden" name="csrf_token" value="<?= h($csrf) ?>">
                <input type="hidden" name="acao" value="adicionar-formacao">
                <div class="linha-campos">
                    <div><label for="formacao-inicio">Data inicial</label><input class="campo" id="formacao-inicio" type="date" name="txtInicio" required></div>
                    <div><label for="formacao-fim">Data final</label><input class="campo" id="formacao-fim" type="date" name="txtFim" required></div>
                </div>
                <label for="formacao-descricao">Curso / instituição / descrição</label>
                <input class="campo" id="formacao-descricao" name="txtDescricao" maxlength="150" required>
                <button class="botao-principal botao-adicionar" type="submit" aria-label="Adicionar"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="9" cy="7" r="4"/><path d="M2 21v-2a7 7 0 0 1 14 0v2M19 8v8M15 12h8"/></svg></button>
            </form>
            <?php tabelaRegistros($formacoes, 'formacao', ['Início', 'Fim', 'Descrição'], 'idformacaoAcademica'); ?>
        </section>

        <section class="secao" id="experiencia">
            <h2 class="titulo-secao"><i class="fa fa-briefcase"></i> Experiência profissional</h2>
            <form method="post" action="index.php?pagina=principal">
                <input type="hidden" name="csrf_token" value="<?= h($csrf) ?>">
                <input type="hidden" name="acao" value="adicionar-experiencia">
                <div class="linha-campos">
                    <div><label for="experiencia-inicio">Data inicial</label><input class="campo" id="experiencia-inicio" type="date" name="txtInicio" required></div>
                    <div><label for="experiencia-fim">Data final</label><input class="campo" id="experiencia-fim" type="date" name="txtFim" required></div>
                </div>
                <label for="empresa">Empresa</label>
                <input class="campo" id="empresa" name="txtEmpresa" maxlength="45" required>
                <label for="experiencia-descricao">Descrição</label>
                <input class="campo" id="experiencia-descricao" name="txtDescricao" maxlength="45" required>
                <button class="botao-principal botao-adicionar" type="submit" aria-label="Adicionar"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="9" cy="7" r="4"/><path d="M2 21v-2a7 7 0 0 1 14 0v2M19 8v8M15 12h8"/></svg></button>
            </form>
            <?php tabelaRegistros($experiencias, 'experiencia', ['Início', 'Fim', 'Empresa', 'Descrição'], 'idexperienciaprofissional'); ?>
        </section>

        <section class="secao" id="outras-formacoes">
            <h2 class="titulo-secao"><i class="fa fa-book"></i> Outras formações</h2>
            <form method="post" action="index.php?pagina=principal">
                <input type="hidden" name="csrf_token" value="<?= h($csrf) ?>">
                <input type="hidden" name="acao" value="adicionar-outra-formacao">
                <div class="linha-campos">
                    <div><label for="outras-inicio">Data inicial</label><input class="campo" id="outras-inicio" type="date" name="txtInicio" required></div>
                    <div><label for="outras-fim">Data final</label><input class="campo" id="outras-fim" type="date" name="txtFim" required></div>
                </div>
                <label for="outras-descricao">Descrição</label>
                <input class="campo" id="outras-descricao" name="txtDescricao" maxlength="150" required>
                <button class="botao-principal botao-adicionar" type="submit" aria-label="Adicionar"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="9" cy="7" r="4"/><path d="M2 21v-2a7 7 0 0 1 14 0v2M19 8v8M15 12h8"/></svg></button>
            </form>
            <?php tabelaRegistros($outrasFormacoes, 'outras', ['Início', 'Fim', 'Descrição'], 'idoutrasformacoes'); ?>
        </section>

        <footer>Sistema de Currículos</footer>
    </main>
</body>
</html>
