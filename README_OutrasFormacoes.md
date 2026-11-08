# Módulo Outras Formações

## Objetivo

Permitir cadastrar, listar e remover outras formações vinculadas a um usuário, informando data inicial, data final e descrição.

## Estrutura de arquivos

- `outras-formacoes.php`: ponto de entrada do módulo.
- `Controller/OutrasFormacoesController.php`: recebe as ações do formulário, valida os dados, chama o Model e prepara os dados da View.
- `Model/OutrasFormacoes.php`: executa as operações no banco de dados.
- `Model/ConexaoBD.php`: cria a conexão MySQL a partir da configuração local.
- `View/outras-formacoes/index.php`: apresenta o formulário, mensagens e tabela de registros.
- `Model/schema.sql`: estrutura do banco e das tabelas.

## Configuração

1. Crie o banco e as tabelas executando `Model/schema.sql` no MySQL.
2. Copie `Model/config.local.php.example` para `Model/config.local.php`.
3. Informe host, porta, banco, usuário e senha do MySQL em `config.local.php`.
4. Não envie `config.local.php` com credenciais para o GitHub.

## Acesso

Com Apache e PHP configurados, acesse:

`http://localhost/ead-tec-dsIII-projeto-final/outras-formacoes.php`

## Operações e parâmetros

### Inserir

- Método HTTP: `POST`
- Campo de ação: `acao=adicionar`
- Parâmetros:
  - `txtInicioOF`: data inicial no formato `YYYY-MM-DD`.
  - `txtFimOF`: data final no formato `YYYY-MM-DD`.
  - `txtDescEP`: descrição obrigatória, com até 150 caracteres.
  - `csrf_token`: token de validação da sessão.

Regras: as duas datas devem ser válidas; a data final não pode anteceder a inicial; a descrição não pode ficar vazia nem ultrapassar 150 caracteres.

### Remover

- Método HTTP: `POST`
- Campo de ação: `acao=apagar`
- Parâmetros:
  - `idoutrasformacoes`: identificador inteiro do registro.
  - `csrf_token`: token de validação da sessão.

A remoção considera o identificador do registro e o identificador do usuário associado.

### Listar

A listagem é realizada pelo Controller por meio de `listarOutrasFormacoes($idUsuario)`. Os registros são apresentados em ordem decrescente de data inicial.

## Mensagens e fluxo

Após uma operação POST, o Controller guarda uma mensagem temporária na sessão e redireciona para a página do módulo usando HTTP 303 (POST/Redirect/GET). Isso evita repetir a operação ao atualizar a página.

Mensagens previstas incluem confirmação de inclusão, confirmação de remoção, dados inválidos, token inválido, registro não encontrado e falha de acesso ao banco.

## Dependências

- PHP com suporte a sessões, extensão MySQLi e funções de data.
- MySQL.
- Apache (ou servidor web PHP equivalente).
- Banco criado conforme `Model/schema.sql`.

## Observação sobre o usuário

O repositório consultado não contém, até o momento, a tela principal do portal nem um mecanismo de contexto de usuário integrado a ela. Para viabilizar testes locais sem implementar autenticação nova, o Controller usa provisoriamente o primeiro usuário cadastrado na tabela `usuario`. Na integração com a aplicação completa, esse valor deve ser substituído pelo identificador do usuário já selecionado/autenticado pelo sistema existente.

## Plano de testes manuais

Execute os casos abaixo no ambiente local e registre o resultado real (aprovado/reprovado), a data e, quando aplicável, a captura de tela. Os resultados não são presumidos por este documento.

| Caso | Procedimento | Resultado esperado |
|---|---|---|
| T01 – Inserção válida | Informar datas válidas, data final igual ou posterior à inicial e descrição com até 150 caracteres; adicionar. | Mensagem de sucesso e registro exibido na tabela. |
| T02 – Datas inválidas | Enviar data ausente ou em formato inválido, desabilitando a validação do navegador se necessário. | Mensagem de erro; nenhum registro incluído. |
| T03 – Período inválido | Informar data final anterior à inicial. | Mensagem de erro; nenhum registro incluído. |
| T04 – Descrição inválida | Enviar descrição vazia ou com mais de 150 caracteres. | Mensagem de erro; nenhum registro incluído. |
| T05 – Remover existente | Clicar em apagar em um registro listado e confirmar. | Mensagem de sucesso e registro removido da tabela. |
| T06 – Remover inexistente | Enviar manualmente um ID inexistente. | Mensagem de erro; nenhum registro é removido. |
| T07 – Listagem vazia | Usar usuário sem registros associados. | A tabela apresenta “Nenhuma formação cadastrada”. |
| T08 – Atualizar após POST | Inserir ou remover e atualizar a página. | A operação não é repetida; a página permanece na listagem. |
| T09 – Token inválido | Enviar formulário com token CSRF inválido. | Mensagem de erro e operação não executada. |

## Registro dos testes

Preencha após executar cada caso:

| Caso | Resultado (aprovado/reprovado) | Observação/captura |
|---|---|---|
| T01 | A preencher | |
| T02 | A preencher | |
| T03 | A preencher | |
| T04 | A preencher | |
| T05 | A preencher | |
| T06 | A preencher | |
| T07 | A preencher | |
| T08 | A preencher | |
| T09 | A preencher | |
