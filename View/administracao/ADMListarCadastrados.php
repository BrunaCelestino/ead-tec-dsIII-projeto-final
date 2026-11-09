<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Usuários cadastrados</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body class="w3-light-grey">
<main class="w3-content w3-padding-32" style="max-width:1000px">
    <header class="w3-container w3-center">
        <h1 class="w3-panel w3-cyan w3-text-white w3-round-large">Usuários cadastrados no sistema</h1>
    </header>
    <div class="w3-responsive w3-white w3-card w3-padding">
        <table class="w3-table-all w3-centered">
            <thead><tr class="w3-blue"><th>Código</th><th>Nome</th><th>CPF</th><th>E-mail</th></tr></thead>
            <tbody>
            <?php if (empty($usuariosCadastrados)): ?>
                <tr><td colspan="4">Nenhum usuário cadastrado.</td></tr>
            <?php else: foreach ($usuariosCadastrados as $row): ?>
                <tr>
                    <td><?= (int)$row['idusuario'] ?></td>
                    <td><?= htmlspecialchars((string)$row['nome'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string)$row['cpf'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string)($row['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
    <p class="w3-center w3-padding-16">
        <a class="w3-button w3-blue w3-round-large" href="index.php?area=admin&pagina=principal">Voltar ao painel</a>
    </p>
</main>
</body>
</html>
