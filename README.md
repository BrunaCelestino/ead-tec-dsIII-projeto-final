Camada Model da Agenda 11.

ConexaoBD.php abre a conexao MySQL usando config.local.php.
Usuario.php implementa insercao, consulta, alteracao, exclusao e listagem.
FormacaoAcad.php implementa operacoes da formacao academica.
ExperienciaProfissional.php implementa operacoes da experiencia profissional.
OutrasFormacoes.php implementa operacoes de outras formacoes por usuario.
schema.sql cria o banco projeto_final e as quatro tabelas do modelo.

As consultas usam prepared statements. A senha de usuario e armazenada com password_hash.