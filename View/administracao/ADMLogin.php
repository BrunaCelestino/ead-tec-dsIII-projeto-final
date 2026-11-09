<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login administrativo - Sistema de Currículos</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body class="w3-light-grey">
<main class="w3-container" style="max-width:480px;margin:8vh auto">
    <form action="index.php?area=admin" method="post" class="w3-card-4 w3-white w3-padding-large w3-round-large">
        <h2 class="w3-center w3-text-blue">Sistema de Currículos</h2>
        <h3 class="w3-center">Área do administrador</h3>
        <?php if (!empty($flash)): ?>
            <div class="w3-panel <?= ($flash['tipo'] ?? '') === 'erro' ? 'w3-pale-red' : 'w3-pale-green' ?>">
                <?= htmlspecialchars((string)($flash['texto'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>
        <input type="hidden" name="area" value="admin">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="acao_admin" value="login">
        <label for="cpf_admin">CPF</label>
        <input class="w3-input w3-border w3-round" id="cpf_admin" name="cpf_admin" type="text" inputmode="numeric" maxlength="14" required>
        <label for="senha_admin" class="w3-margin-top">Senha</label>
        <input class="w3-input w3-border w3-round" id="senha_admin" name="senha_admin" type="password" required>
        <button class="w3-button w3-blue w3-block w3-round w3-margin-top" type="submit">Entrar</button>
        <a class="w3-button w3-light-grey w3-block w3-round w3-margin-top" href="index.php">Voltar ao portal</a>
    </form>
</main>
</body>
</html>
