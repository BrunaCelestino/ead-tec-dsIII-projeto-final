<?php
function h($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"><title>Login - Sistema de Currículos</title></head>
<body class="w3-light-grey">
<main class="w3-container" style="max-width:480px;margin:7vh auto">
<form action="index.php" method="post" class="w3-card-4 w3-white w3-padding-large w3-round-large">
<h2 class="w3-center w3-text-blue">Sistema de Currículos</h2><h3 class="w3-center">Login</h3>
<?php if ($flash): ?><div class="w3-panel <?= ($flash['tipo'] ?? '') === 'erro' ? 'w3-pale-red' : 'w3-pale-green' ?>"><?= h($flash['texto'] ?? '') ?></div><?php endif; ?>
<input type="hidden" name="acao" value="login">
<label for="txtLogin">CPF</label><input class="w3-input w3-border w3-round" id="txtLogin" name="txtLogin" type="text" inputmode="numeric" maxlength="14" placeholder="Digite seu CPF" required>
<label for="txtSenha" class="w3-margin-top">Senha</label><input class="w3-input w3-border w3-round" id="txtSenha" name="txtSenha" type="password" required>
<button class="w3-button w3-blue w3-block w3-round w3-margin-top" type="submit">Entrar</button>
<a class="w3-button w3-light-grey w3-block w3-round w3-margin-top" href="index.php?pagina=primeiro-acesso">Primeiro acesso</a>\n<a class="w3-button w3-white w3-block w3-round w3-margin-top" href="administracao.php">Acesso administrativo</a>
</form></main></body></html>
