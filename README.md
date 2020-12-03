#### Requisitos para iniciar o projeto:

Composer

PHP 7.3

Banco de dados (Postgres ou MySQL)

#### Comandos para iniciar o projeto

composer install

copy .env.example .env

php artisan key:generate

(Preencher .env com informações do banco de dados)

php artisan migrate

php artisan db:seed

php artisan serve

#### Link para as collections do postman:
https://documenter.getpostman.com/view/2138423/TVmMgdL3

#### Tomadas de decisões no projeto:

##### Banco de dados:

O banco de dados foi montado 4 tabelas, sendo elas Classifications referente à classificação indicativas do filme, Movies referente aos filmes, Directors referente aos diretores e Actors referente aos atores.
Cada filme possui 1 classificação indicativa, sendo assim foi adicionado a coluna classification_id como chave estrangeira.
Cada filme pode possuir 1 ou mais diretores e 1 ou mais atores, da mesma forma que tanto diretores quanto atores podem atuar em mais de 1 filmes, com isso foram criadas duas tabelas para o relacionamento muitos para muitos, sendo elas movie_director e movie_actors.

##### Seed:
Para alimentação inicial do banco de dados foi adicionado 1 seed com apenas uma informação de cada tabela.

##### Rotas:
As rotas seguem o padrão da apiresource do laravel, 5 rotas para cada controllers, sendo elas: index, store, show, update e destroy.

##### Models:
Defini o uso do SoftDeletes para cada model para caso a informação seja deletada ela continue disponível para consultas administrativas futuras ou desfazer a operação.
Os relacionamentos também foram definidos em cada um dos models para aproveitar suas funções dentro do controller.

##### Controllers:
Em cada um dos controllers foi criado a função jsonResponse para retornar o json já configurado com o charset UTF-8, com isso não precisa ser informado em cada retorno, apenas chamamos a função e passamos o objeto e código de resposta.

Foi utilizado a função valitador do laravel para validarmos os dados que estão entrado via requisição, se eles não estiverem de acordo já retornamos um erro 400 e o motivo do erro.

A função DB::beginTransaction() foi utilizada para garantir que os dados serão incluídos por completo ou revertidos, após inicia-la chamamos um try catch, caso toda operação seja concluída com sucesso damos um DB::commit() e retornamos com os dados, se em algum momento da operação tivermos um erro é chamada a função DB::rollBack() e quaisquer dados que tenham sido inseridos no banco à partir do beginTransaction serão removidos.

No caso do relacionamento 1 para muitos de filmes e classificação utilizamos a função associate, com isso conseguimos inserir ID de classificação nos filmes.

No relacionamento de muitos para muitos foi utilizado o método sync, com isso cada vez que ele é chamado é feito uma nova sincronia dos dados.

No método INDEX e SHOW é retornado das relações no corpo da resposta.
