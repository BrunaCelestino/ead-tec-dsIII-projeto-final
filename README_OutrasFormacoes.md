# Módulo Outras Formações

Este módulo permite que o usuário cadastre, consulte e exclua cursos e outras formações complementares em seu currículo.

## Estrutura

O módulo utiliza a arquitetura MVC do portal:

- **Model – `Model/OutrasFormacoes.php`:** representa os registros de outras formações e realiza as operações no banco de dados.
- **View – seção Outras Formações em `View/principal.php`:** apresenta o formulário de cadastro e a tabela com os registros do usuário.
- **Controller – operações integradas ao fluxo principal:** recebe as ações da interface, valida os dados e aciona o Model.
- **Navegação – `Controller/Navegacao.php`:** direciona as requisições para a página principal e processa as ações do usuário.

## Dados armazenados

A tabela `outrasformacoes` possui os seguintes campos:

| Campo | Descrição |
|---|---|
| `idoutrasformacoes` | Identificador do registro |
| `idusuario` | Usuário proprietário da formação |
| `inicio` | Data inicial |
| `fim` | Data final |
| `descricao` | Descrição da formação, com até 150 caracteres |

Cada registro é associado ao usuário por meio de `idusuario`.

## Funcionalidades

### Inserir uma formação

O usuário informa a data inicial, a data final e a descrição no formulário da página principal. Ao acionar o botão de adicionar, os dados são encaminhados ao processamento do módulo e registrados no banco de dados.

### Listar formações

Ao acessar a página principal, o sistema consulta as outras formações vinculadas ao usuário autenticado e apresenta os registros na tabela.

### Remover uma formação

O botão **Apagar** permite excluir um registro. A operação considera o identificador da formação e o identificador do usuário autenticado, impedindo a exclusão de registros pertencentes a outra conta.

## Validações e segurança

- Os registros são vinculados ao usuário autenticado.
- Os dados são enviados ao banco por meio de consultas preparadas.
- As operações de inclusão e exclusão são realizadas por requisições POST.
- As ações protegidas utilizam token CSRF.
- A descrição respeita o limite definido no banco de dados.

## Banco de dados

A tabela é criada pelo script `Model/schema.sql`, no banco `projeto_final`. Sua estrutura utiliza chave primária própria e chave estrangeira para a tabela `usuario`.

## Testes realizados

Foram testadas as operações de cadastro, listagem e exclusão de outras formações pela interface do portal, verificando a atualização dos registros apresentados ao usuário.

## Tecnologias utilizadas

- PHP
- MySQL
- HTML
- CSS
- Arquitetura MVC
