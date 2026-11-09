<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Painel administrativo</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body class="w3-light-grey">
<main class="w3-content" style="max-width:900px">
    <header class="w3-container w3-padding-32 w3-center">
        <h1 class="w3-panel w3-cyan w3-text-white w3-round-large">ADMINISTRAÇÃO</h1>
        <h2>Sistema de Currículos</h2>
        <?php if (!empty($flash)): ?>
            <div class="w3-panel <?= ($flash['tipo'] ?? '') === 'erro' ? 'w3-pale-red' : 'w3-pale-green' ?>">
                <?= htmlspecialchars((string)($flash['texto'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>
    </header>
    <section class="w3-row-padding w3-center">
        <div class="w3-half w3-margin-bottom">
            <a href="index.php?area=admin&pagina=usuarios" class="w3-button w3-blue w3-round-large w3-padding-32 w3-block">
                <i class="fa fa-users w3-xxlarge"></i><br>Usuários cadastrados
            </a>
        </div>
        <div class="w3-half w3-margin-bottom">
            <a href="index.php?area=admin&pagina=administradores" class="w3-button w3-blue w3-round-large w3-padding-32 w3-block">
                <i class="fa fa-address-card w3-xxlarge"></i><br>Administradores cadastrados
            </a>
        </div>
    </section>
    <form action="index.php?area=admin" method="post" class="w3-center w3-padding-16">
        <input type="hidden" name="area" value="admin">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="acao_admin" value="sair">
        <button class="w3-button w3-red w3-round-large" type="submit">Sair</button>
        <a class="w3-button w3-light-grey w3-round-large" href="index.php">Portal do usuário</a>
    </form>
</main>
</body>
</html>
