# Sistema de Currículos

Projeto acadêmico desenvolvido em PHP orientado a objetos, com arquitetura MVC e banco MySQL.

## Tecnologias
- PHP com MySQLi
- MySQL
- HTML, CSS e W3.CSS
- Sessões PHP

## Configuração local
1. Clone o repositório para a pasta pública do Apache.
2. Crie o banco executando `Model/schema.sql` no MySQL.
3. Copie `Model/config.local.php.example` para `Model/config.local.php`.
4. Preencha host, porta, banco, usuário e senha do MySQL. Não publique o arquivo `config.local.php`.
5. Habilite no PHP as extensões `mysqli` e `mysqlnd` (para `get_result()`).
6. Acesse `http://localhost/ead-tec-dsIII-projeto-final/`.

## Fluxo do portal
- `index.php`: ponto de entrada.
- `Controller/PortalController.php`: ponto central de navegação, autenticação, cadastro, sessão, validação CSRF e atualização de dados.
- `Controller/ExperienciaProfissionalController.php`: inclusão, remoção segura e listagem das experiências profissionais.
- `View/login.php`: login por CPF e senha.
- `View/primeiroAcesso.php`: cadastro inicial.
- `View/principal.php`: página central com dados pessoais, formação acadêmica, experiência profissional e outras formações.
- `Model/`: persistência dos dados.

O cadastro armazena a senha com `password_hash`; o login verifica com `password_verify`. As operações de inclusão e exclusão exigem token CSRF. As exclusões de formação e experiência são limitadas ao usuário dono do registro.

## Módulo Outras Formações
O módulo original permanece disponível em `outras-formacoes.php` e documentado em [README_OutrasFormacoes.md](README_OutrasFormacoes.md). Ele é mantido como implementação independente, sem duplicar a funcionalidade integrada à página principal.

## Observações
- O banco deve ser criado antes do primeiro acesso.
- O primeiro usuário deve ser cadastrado pela opção **Primeiro acesso**.
- A tabela de experiência profissional possui campos `empresa` e `descricao` com limite de 45 caracteres, conforme o schema atual.
- O projeto não inclui recuperação de senha, envio de e-mail ou painel administrativo nesta etapa.
