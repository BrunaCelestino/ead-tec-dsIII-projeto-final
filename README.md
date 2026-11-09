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
- `index.php`: ponto único de entrada do sistema.
- `Controller/Navegacao.php`: seleciona a View solicitada, verifica a sessão e carrega os dados necessários para a tela principal.
- `Controller/PortalController.php`: processa as ações enviadas pelos formulários, incluindo autenticação, cadastro, sessão, validação CSRF e atualização de dados.
- `Controller/ExperienciaProfissionalController.php`: inclusão, remoção segura e listagem das experiências profissionais.
- `Controller/AdministradorController.php`: autenticação administrativa e consultas de usuários e administradores.
- `administracao.php`: entrada da área administrativa, roteada por `Controller/Navegacao.php`.
- `View/administracao/`: login, painel e listagens administrativas.
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
- O painel administrativo é acessado por `http://localhost/ead-tec-dsIII-projeto-final/administracao.php`.
- O schema cria um administrador demonstrativo com CPF `22222222222` e senha inicial `bia123`. No primeiro login válido, a senha legada é convertida para hash. Altere essa credencial em ambientes não demonstrativos.
