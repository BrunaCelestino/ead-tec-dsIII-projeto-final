# Sistema de Currículos

Projeto acadêmico desenvolvido em PHP orientado a objetos, com arquitetura MVC e banco de dados MySQL.

## Tecnologias
- PHP com MySQLi
- MySQL
- HTML, CSS e W3.CSS
- Sessões PHP

## Estrutura do projeto

```text
/
├── index.php                         # Único ponto de entrada (Front Controller)
├── Controller/
│   ├── Navegacao.php                 # Roteamento e seleção das Views
│   ├── PortalController.php           # Ações do usuário, sessão e validações
│   ├── AdministradorController.php    # Autenticação e consultas administrativas
│   └── ExperienciaProfissionalController.php
├── Model/
│   ├── ConexaoBD.php                  # Conexão com MySQL
│   ├── Usuario.php
│   ├── Administrador.php
│   ├── FormacaoAcad.php
│   ├── ExperienciaProfissional.php
│   ├── OutrasFormacoes.php
│   ├── config.local.php.example
│   └── schema.sql
└── View/
    ├── login.php
    ├── primeiroAcesso.php
    ├── principal.php
    └── administracao/
        ├── ADMLogin.php
        ├── ADMPrincipal.php
        ├── ADMListarCadastrados.php
        └── ADMListarAdministradores.php
```

### Responsabilidades MVC

- **Model:** representa os dados e concentra as operações de persistência no MySQL.
- **View:** apresenta as telas e os dados preparados pelos Controllers.
- **Controller:** recebe as ações dos formulários, valida as entradas e coordena as operações dos Models.
- **Navegação:** atua como roteador, selecionando a área e a View correspondente.

O arquivo `index.php` é o único ponto de entrada. O portal do usuário é acessado pela raiz; a área administrativa usa `index.php?area=admin`, sem um segundo arquivo PHP na raiz.

## Configuração local

1. Clone o repositório para a pasta pública do Apache.
2. Crie o banco executando `Model/schema.sql` no MySQL.
3. Copie `Model/config.local.php.example` para `Model/config.local.php`.
4. Preencha host, porta, banco, usuário e senha do MySQL.
5. Não publique o arquivo `config.local.php`.
6. Habilite no PHP as extensões `mysqli` e `mysqlnd` (necessária para `get_result()`).
7. Acesse `http://localhost/ead-tec-dsIII-projeto-final/`.

## Funcionalidades

- Cadastro inicial e login de usuários por CPF e senha.
- Atualização de dados pessoais.
- Inclusão, listagem e exclusão de formação acadêmica.
- Inclusão, listagem e exclusão de experiências profissionais.
- Inclusão, listagem e exclusão de outras formações.
- Login administrativo.
- Listagem de usuários e administradores cadastrados.

O cadastro armazena senhas com `password_hash`; o login utiliza `password_verify`. As operações protegidas por formulário validam token CSRF. As exclusões de formações e experiências são limitadas ao usuário proprietário do registro.

## Acesso administrativo

Acesse `http://localhost/ead-tec-dsIII-projeto-final/index.php?area=admin`.

O schema cria um administrador demonstrativo com CPF `22222222222` e senha inicial `bia123`. No primeiro login válido, a senha legada é convertida para hash. Altere essa credencial em ambientes não demonstrativos.

## Observações

- O banco deve ser criado antes do primeiro acesso.
- O primeiro usuário deve ser cadastrado pela opção **Primeiro acesso**.
- A tabela de experiência profissional possui campos `empresa` e `descricao` com limite de 45 caracteres, conforme o schema atual.
