<?php
require 'C:/Apache24/Model/OutrasFormacoes.php';

$con = (new ConexaoBD())->conectar();
$nome = 'Usuario temporario de teste';
$senha = 'Teste123';
$cpf = '';
$usuarioId = null;

$stmt = $con->prepare('INSERT INTO usuario (nome, cpf, senha) VALUES (?, ?, ?)');
$stmt->bind_param('sss', $nome, $cpf, $senha);

for ($tentativa = 0; $tentativa < 5; $tentativa++) {
    $cpf = str_pad((string) random_int(0, 99999999999), 11, '0', STR_PAD_LEFT);
    try {
        $stmt->execute();
        $usuarioId = $con->insert_id;
        break;
    } catch (mysqli_sql_exception $e) {
        if ((int)$e->getCode() !== 1062 || $tentativa === 4) {
            $stmt->close();
            $con->close();
            throw $e;
        }
    }
}
$stmt->close();
$con->close();

if ($usuarioId === null) {
    throw new RuntimeException('Nao foi possivel criar o usuario temporario.');
}
echo "Usuario temporario criado: $usuarioId\n";

$formacao = new OutrasFormacoes();
$formacao->setIDUsuario((int)$usuarioId);
$formacao->setInicio(date('Y-m-d'));
$formacao->setFim(date('Y-m-d'));
$formacao->setDescricao('Teste temporario');
$formacaoId = null;

try {
    if (!$formacao->inserirBD()) {
        throw new RuntimeException('Falha ao inserir: ' . $formacao->getUltimoErro());
    }
    $formacaoId = $formacao->getID();
    echo "Insercao: OK\n";

    $lista = $formacao->listarOutrasFormacoes((int)$usuarioId);
    $encontrada = false;
    foreach ($lista as $linha) {
        if ((int)$linha['idoutrasformacoes'] === $formacaoId) {
            $encontrada = true;
            break;
        }
    }
    if (!$encontrada) {
        throw new RuntimeException('O registro inserido nao apareceu na listagem.');
    }
    echo "Listagem: OK\n";

    $formacao->setID($formacaoId);
    $formacao->setDescricao('Teste atualizado');
    if (!$formacao->alterarBD()) {
        throw new RuntimeException('Falha ao alterar: ' . $formacao->getUltimoErro());
    }
    echo "Alteracao: OK\n";
} finally {
    $registroRemovido = $formacaoId === null || $formacao->excluirBD($formacaoId);
    if ($registroRemovido) {
        echo "Exclusao da formacao: OK\n";
    } else {
        echo "Atencao: remova a formacao ID $formacaoId antes de excluir o usuario.\n";
    }

    if ($registroRemovido) {
        $con = (new ConexaoBD())->conectar();
        $stmt = $con->prepare('DELETE FROM usuario WHERE idusuario = ?');
        $stmt->bind_param('i', $usuarioId);
        $stmt->execute();
        $stmt->close();
        $con->close();
        echo "Exclusao do usuario temporario: OK\n";
    }
}