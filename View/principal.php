<?php
function h($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function dataBR($v): string { if (!$v) return ''; $d=DateTime::createFromFormat('Y-m-d',(string)$v); return $d ? $d->format('d/m/Y') : h($v); }
function tabelaRegistros(array $rows, string $tipo, array $colunas): void {
 echo '<div class="w3-responsive"><table class="w3-table-all w3-margin-bottom"><thead><tr>';
 foreach ($colunas as $label) echo '<th>'.h($label).'</th>';
 echo '<th>Apagar</th></tr></thead><tbody>';
 if (!$rows) echo '<tr><td colspan="'.(count($colunas)+1).'" class="w3-center">Nenhum registro cadastrado.</td></tr>';
 foreach ($rows as $r) {
  echo '<tr><td>'.h(dataBR($r['inicio'] ?? '')).'</td><td>'.h(dataBR($r['fim'] ?? '')).'</td>';
  if ($tipo === 'experiencia') echo '<td>'.h($r['empresa'] ?? '').'</td>';
  echo '<td>'.h($r['descricao'] ?? '').'</td><td><form method="post" action="index.php?pagina=principal" onsubmit="return confirm(\'Deseja excluir este registro?\')">';
  echo '<input type="hidden" name="csrf_token" value="'.h($GLOBALS['csrf']).'"><input type="hidden" name="idRegistro" value="'.(int)$r[array_key_first(array_filter(array_keys($r),fn($k)=>str_starts_with($k,'id') && $k!=='idusuario'))].'">';
  echo '<input type="hidden" name="acao" value="excluir-'.($tipo==='experiencia'?'experiencia':($tipo==='outras'?'outra-formacao':'formacao')).'"><button class="w3-button w3-red w3-small" type="submit">Apagar</button></form></td></tr>';
 }
 echo '</tbody></table></div>';
}
?>
<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"><title>Sistema de Currículos</title>
<style>body{font-family:Arial,sans-serif}.w3-sidebar{width:190px}.conteudo{margin-left:190px}.secao{max-width:1000px;margin:auto}.w3-input{margin-bottom:12px}@media(max-width:600px){.w3-sidebar{width:100%;height:auto;position:relative}.conteudo{margin-left:0}}</style></head>
<body class="w3-light-grey">
<nav class="w3-sidebar w3-bar-block w3-blue w3-card"><div class="w3-container w3-padding-16"><b>SISTEMA DE<br>CURRÍCULOS</b></div>
<a href="#home" class="w3-bar-item w3-button"><i class="fa fa-home"></i> Home</a><a href="#dPessoais" class="w3-bar-item w3-button"><i class="fa fa-user"></i> Dados pessoais</a><a href="#formacao" class="w3-bar-item w3-button"><i class="fa fa-graduation-cap"></i> Formação</a><a href="#experiencia" class="w3-bar-item w3-button"><i class="fa fa-briefcase"></i> Experiência</a><a href="#outras" class="w3-bar-item w3-button"><i class="fa fa-book"></i> Outras formações</a>
<form method="post" action="index.php" class="w3-container w3-padding"><input type="hidden" name="csrf_token" value="<?= h($csrf) ?>"><input type="hidden" name="acao" value="sair"><button class="w3-button w3-white w3-block" type="submit"><i class="fa fa-sign-out"></i> Sair</button></form></nav>
<main class="conteudo w3-padding-large">
<?php if ($flash): ?><div class="w3-panel <?= ($flash['tipo'] ?? '')==='erro'?'w3-pale-red':'w3-pale-green' ?> w3-display-container"><span onclick="this.parentElement.style.display='none'" class="w3-button w3-display-topright">&times;</span><?= h($flash['texto'] ?? '') ?></div><?php endif; ?>
<header id="home" class="w3-container w3-padding-32 w3-center"><h1 class="w3-text-blue">Bem-vindo(a), <?= h($usuarioLogado->getNome()) ?>!</h1><p>Gerencie as informações do seu currículo.</p></header>
<section id="dPessoais" class="secao w3-card w3-white w3-padding-large w3-margin-bottom"><h2 class="w3-text-blue">Dados Pessoais</h2>
<form method="post" action="index.php?pagina=principal"><input type="hidden" name="csrf_token" value="<?= h($csrf) ?>"><input type="hidden" name="acao" value="atualizar-dados">
<label>Nome</label><input class="w3-input w3-border" name="txtNome" value="<?= h($usuarioLogado->getNome()) ?>" required maxlength="150">
<label>CPF</label><input class="w3-input w3-border" name="txtCPF" value="<?= h($usuarioLogado->getCPF()) ?>" required maxlength="11" inputmode="numeric">
<label>Data de nascimento</label><input class="w3-input w3-border" type="date" name="txtData" value="<?= h($usuarioLogado->getDataNascimento()) ?>">
<label>E-mail</label><input class="w3-input w3-border" type="email" name="txtEmail" value="<?= h($usuarioLogado->getEmail()) ?>" maxlength="150">
<button class="w3-button w3-blue" type="submit">Atualizar dados</button></form></section>
<section id="formacao" class="secao w3-card w3-white w3-padding-large w3-margin-bottom"><h2 class="w3-text-blue">Formação Acadêmica</h2>
<form method="post" action="index.php?pagina=principal"><input type="hidden" name="csrf_token" value="<?= h($csrf) ?>"><input type="hidden" name="acao" value="adicionar-formacao">
<div class="w3-row-padding"><div class="w3-half"><label>Data inicial</label><input class="w3-input w3-border" type="date" name="txtInicio" required></div><div class="w3-half"><label>Data final</label><input class="w3-input w3-border" type="date" name="txtFim" required></div></div><label>Curso / instituição / descrição</label><input class="w3-input w3-border" name="txtDescricao" maxlength="150" required><button class="w3-button w3-blue" type="submit">Adicionar formação</button></form>
<?php tabelaRegistros($formacoes,'formacao',['Início','Fim','Descrição']); ?></section>
<section id="experiencia" class="secao w3-card w3-white w3-padding-large w3-margin-bottom"><h2 class="w3-text-blue">Experiência Profissional</h2>
<form method="post" action="index.php?pagina=principal"><input type="hidden" name="csrf_token" value="<?= h($csrf) ?>"><input type="hidden" name="acao" value="adicionar-experiencia">
<div class="w3-row-padding"><div class="w3-half"><label>Data inicial</label><input class="w3-input w3-border" type="date" name="txtInicio" required></div><div class="w3-half"><label>Data final</label><input class="w3-input w3-border" type="date" name="txtFim" required></div></div><label>Empresa</label><input class="w3-input w3-border" name="txtEmpresa" maxlength="45" required><label>Descrição</label><input class="w3-input w3-border" name="txtDescricao" maxlength="45" required><button class="w3-button w3-blue" type="submit">Adicionar experiência</button></form>
<?php tabelaRegistros($experiencias,'experiencia',['Início','Fim','Empresa','Descrição']); ?></section>
<section id="outras" class="secao w3-card w3-white w3-padding-large w3-margin-bottom"><h2 class="w3-text-blue">Outras Formações</h2>
<form method="post" action="index.php?pagina=principal"><input type="hidden" name="csrf_token" value="<?= h($csrf) ?>"><input type="hidden" name="acao" value="adicionar-outra-formacao">
<div class="w3-row-padding"><div class="w3-half"><label>Data inicial</label><input class="w3-input w3-border" type="date" name="txtInicio" required></div><div class="w3-half"><label>Data final</label><input class="w3-input w3-border" type="date" name="txtFim" required></div></div><label>Descrição</label><input class="w3-input w3-border" name="txtDescricao" maxlength="150" required><button class="w3-button w3-blue" type="submit">Adicionar outra formação</button></form>
<?php tabelaRegistros($outrasFormacoes,'outras',['Início','Fim','Descrição']); ?></section>
<footer class="w3-center w3-padding-32">Sistema de Currículos</footer></main></body></html>
