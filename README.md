# Desafio Backend GrandChef

## Descrição do Projeto

O objetivo do teste consiste em criar um API para gerenciar categorias, produtos e pedidos. 
A API foi desenvolvida em PHP utilizando o framework Laravel.

## Executando o projeto

#### Dependências

Versões utilizadas:

PHP 8.3.x (com extensão pdo_pgsql)
Composer 2.7.x
PostgreSQL 16.x

### Local

```
compose install
php artisan migrate
php artisan serve
```

### Docker

> Talvez seja necessário alterar o arquivo `.env`, definindo `DB_HOST` para `pgsql`.

```
docker compose up -d pgsql
docker compose up app --build
```

## Endpoints

As seguintes implementações foram disponibilizadas em endpoints REST: criação e listagem de categorias,
criação e listagem de produtos, criação, listagem e atualização de pedidos.

Categorias:
```
GET http://127.0.0.1:8000/api/categories

POST http://127.0.0.1:8000/api/categories
{
  "name": "Nome Categoria"
}
```

Produtos
```
GET http://127.0.0.1:8000/api/products

POST http://127.0.0.1:8000/api/products
{
	"name": "Nome produto",
	"price": 11.1,
	"category_id": 1
}
```

Pedidos
```
GET http://127.0.0.1:8000/api/orders

POST http://127.0.0.1:8000/api/orders
{
	"products": [
		{
			"product_id": 1,
			"price": 20.0,
			"quantity": 1
		}
	]
}

PUT http://127.0.0.1:8000/api/orders/{id}
{
	"status": "completed"
}
```

## Testes

Para executar os testes da aplicação use o comando:

```
php artisan test --coverage
```
