# Projeto final — Desenvolvimento de Sistemas III

Este repositório contém a camada Model em PHP/MySQL e a tela de **Outras Formações**.

## Funcionalidade implementada

A página `outras-formacoes.php` permite ao usuário:
- cadastrar uma formação com data inicial, data final e descrição;
- consultar somente as formações vinculadas à sua conta;
- excluir uma formação cadastrada.

A tela utiliza a classe `Model/OutrasFormacoes.php`, valida as datas e a descrição no servidor, usa consultas preparadas e protege as operações POST com token CSRF. A exclusão também confere o usuário proprietário do registro.

## Configuração

1. Crie o banco e as tabelas executando `Model/schema.sql` no MySQL.
2. Copie `Model/config.local.php.example` para `Model/config.local.php`.
3. Edite `Model/config.local.php` com host, porta, banco, usuário e senha do seu MySQL. Esse arquivo é ignorado pelo Git.
4. No processo de login do sistema, depois de validar as credenciais, armazene o ID do usuário na sessão:

```php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Depois de validar a senha do usuário:
$_SESSION['idusuario'] = $usuario->getID();
```

5. Disponibilize `outras-formacoes.php` pelo Apache/PHP e acesse essa página após entrar no sistema.

> Este repositório ainda não contém a tela de login nem um arquivo principal de navegação. A página foi criada como módulo independente e espera que o login existente defina `$_SESSION['idusuario']`. O ID não é recebido pela URL.

## Estrutura

- `Model/ConexaoBD.php`: conexão com MySQL.
- `Model/Usuario.php`: operações de usuário e validação de senha.
- `Model/FormacaoAcad.php`: operações de formação acadêmica.
- `Model/ExperienciaProfissional.php`: operações de experiência profissional.
- `Model/OutrasFormacoes.php`: operações de cadastro, alteração, listagem e exclusão de outras formações.
- `Model/schema.sql`: criação do banco e das tabelas.
- `outras-formacoes.php`: interface de cadastro e listagem de outras formações.
