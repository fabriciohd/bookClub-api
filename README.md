# bookClub API

API REST para para o aplicativo de clube do livro.

Documentação para acesso via API:
* [**Configurações**](#configs)
* [**Métodos**](#methods)
* [**Respostas**](#responses)
* [**Rotas**](#routes)
* [**Parâmetros**](#params)

<a id="configs"></a>

## Configurações do projeto
Requisitos mínimos:
- [PHP ^7.2](https://www.php.net)
- [Mysql 5.5.5-10.4.17-MariaDB](https://www.mysql.com)
- [Composer](https://getcomposer.org)

Framework utilizado:
- [Laravel ^8.37](https://laravel.com/docs/8.x)
<br>

Primeiramente, ao abrir o projeto, lembre-se de instalar as dependências:
```
composer install
```

Após instalar as dependências, copie o arquivo `.env.example` e renomeie para `.env`, se preciso, faça as configurações necessárias e crie o banco de dados local em sua máquina (nome do banco que está no .env.example: `bookclub_api`).

Rode as migrations para criar as tabelas necessárias:
```
php artisan migrate
```

Rode o projeto localmente para obter a url de acesso:
```
php artisan serve
```
A url deverá ser complementada com /api para acessar as rotas, ex:
http://127.0.0.1:8000/api/

<a id="methods"></a>

## Métodos
Todas as requisições seguem o seguinte padrão:
| Método | Descrição |
|---|---|
| `GET` | Retorna informações de um ou mais registros. |
| `POST` | Cria um novo registro. |
| `DELETE` | Remove um registro. |

<a id="responses"></a>

## Respostas
Prováveis respostas às requisições.
| Código | Descrição |
|---|---|
| `200` | Requisição executada com sucesso.|
| `401` | Dados de acesso inválidos.|
| `404` | Registro ou rota pesquisada não encontrada (Not found).|
| `422` | Erro de validação de campo ou fora do escopo definido para o campo.|

<a id="routes"></a>

## Endpoints
### Rotas Públicas
| Método | Endpoint | Parâmetros Requeridos | Parâmetros Opcionais | Resumo |
|---|---|---|---|---|
| `POST` | /auth/login | `email` `password` | | Cria o token de altenticação pelo email e senha do usuário. |
| `POST` | /auth/register | `name` `email` `password` `password_confirm` | | Cadastra um usuário. |
### Rotas Autenticadas
| Método | Endpoint | Parâmetros Requeridos | Parâmetros Opcionais | Resumo |
|---|---|---|---|---|
| `GET` | /user | | | Retorna as informações do usuário logado. |
| `POST` | /auth/logout | | | Destrói o token de altenticação do usuário. |
| `GET` | /titles | | | Retorna informações de todos os títulos disponíveis. |
| `POST` | /title | [`title`](#title) | | Cadastra um novo título. |
| `GET` | /title/disableddates | [`title_id`](#title_id) | | Retorna as datas indisponíveis do título. |
| `DELETE` | '/title/{id} | | | Remove um título do registro (se não houver reservas futuras para esse título). |
| `POST` | /reservation | [`title_id`](#title_id) [`start_date`](#start_date) [`end_date`](#end_date) | | Cria uma reserva para o título enviado. |

<a id="params"></a>

## Parâmetros
| Parâmetro | GET | POST | PUT | DELETE |
|---|---|---|---|---|
| <a id="title"></a> `title` | | Título do livro/revista que deseja cadastrar. (`string`) | | |
| <a id="title_id"></a> `title_id` | ID do título para busca de informações. (`number`) | ID do título para cadastro de informações. (`number`) | | |
| <a id="start_date"></a> `start_date` | | Primeiro dia da reserva. (`date(YYYY-MM-DD)`) | | |
| <a id="end_date"></a> `start_date` | | Último dia da reserva (deve ser um período menor que 5 dias de `start_date`). (`date(YYYY-MM-DD)`) | | |
