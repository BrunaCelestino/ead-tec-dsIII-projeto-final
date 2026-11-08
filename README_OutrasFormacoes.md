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

## Testes manuais realizados

Os sete cenários abaixo foram registrados como aprovados no relatório da atividade. As evidências correspondentes devem ser mantidas junto ao relatório.

| Caso | Procedimento | Resultado registrado |
|---|---|---|
| T01 – Inserção válida | Cadastrar uma formação com dados válidos. | PASSOU – registro incluído e exibido na listagem. |
| T02 – Inserção inválida | Tentar cadastrar uma formação com dados inválidos. | PASSOU – inclusão bloqueada e mensagem de erro apresentada. |
| T03 – Remoção de registro existente | Remover uma formação cadastrada. | PASSOU – registro removido da listagem. |
| T04 – Remoção de registro inexistente | Tentar remover um identificador que não corresponde a um registro. | PASSOU – operação tratada sem remover outro registro. |
| T05 – Listagem vazia | Consultar a listagem sem formações cadastradas para o usuário. | PASSOU – estado de lista vazia apresentado. |
| T06 – Atualização após operação | Atualizar a página após uma inclusão ou remoção. | PASSOU – a operação não foi repetida. |
| T07 – Redirecionamento após operação | Verificar o retorno à página do módulo após o envio do formulário. | PASSOU – fluxo POST/Redirect/GET executado. |

## Casos adicionais para validação

Os casos abaixo complementam os cenários do relatório. Execute-os no ambiente local e registre o resultado antes de considerá-los aprovados.

| Caso | Procedimento | Resultado esperado |
|---|---|---|
| T08 – Período inválido | Informar data final anterior à inicial. | Mensagem de erro; nenhum registro incluído. |
| T09 – Descrição inválida | Enviar descrição vazia ou com mais de 150 caracteres. | Mensagem de erro; nenhum registro incluído. |
| T10 – Token CSRF inválido | Enviar formulário com token CSRF inválido. | Mensagem de erro e operação não executada. |

Para os casos adicionais, registre o resultado real, a data e, quando aplicável, a captura de tela. Não marque um caso como aprovado sem executá-los.
