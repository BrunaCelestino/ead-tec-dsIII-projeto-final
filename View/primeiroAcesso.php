<?php function h($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); } ?>
<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"><title>Primeiro Acesso</title></head>
<body class="w3-light-grey"><main class="w3-container" style="max-width:560px;margin:4vh auto"><form action="index.php?pagina=primeiro-acesso" method="post" class="w3-card-4 w3-white w3-padding-large w3-round-large">
<h2 class="w3-center w3-text-blue">Primeiro Acesso</h2>
<?php if ($flash): ?><div class="w3-panel <?= ($flash['tipo'] ?? '') === 'erro' ? 'w3-pale-red' : 'w3-pale-green' ?>"><?= h($flash['texto'] ?? '') ?></div><?php endif; ?>
<input type="hidden" name="acao" value="cadastro"><input type="hidden" name="csrf_token" value="<?= h($csrf) ?>">
<label>Nome completo</label><input class="w3-input w3-border w3-margin-bottom" name="txtNome" maxlength="150" required>
<label>CPF (11 números)</label><input class="w3-input w3-border w3-margin-bottom" name="txtCPF" inputmode="numeric" maxlength="14" required>
<label>Data de nascimento</label><input class="w3-input w3-border w3-margin-bottom" type="date" name="txtData">
<label>E-mail</label><input class="w3-input w3-border w3-margin-bottom" type="email" name="txtEmail" maxlength="150">
<label>Senha</label><input class="w3-input w3-border w3-margin-bottom" type="password" name="txtSenha" minlength="8" required>
<button class="w3-button w3-blue w3-block w3-round" type="submit">Cadastrar</button><a href="index.php" class="w3-button w3-light-grey w3-block w3-margin-top">Voltar ao login</a>
</form></main></body></html>
