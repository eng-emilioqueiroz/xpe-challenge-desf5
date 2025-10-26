# Configuração do ambiente

Com o PHP instalado na máquina faça um clone do repositório

# Arquitetura do software

![Diagrama da arquitetura do software](/storage/app/public/diagrama.svg "Diagrama")

# Explicação objetiva da estrutura e dos elementos do código

A API segue o padrão MVC do Laravel, com uma camada extra de Service para isolar regras de negócio:

**Controller:** recebe a requisição HTTP, delega ao Service e devolve a resposta.

**Service:** concentra regras de negócio e orquestra o acesso ao banco via Eloquent.

**Model (Eloquent):** mapeia a tabela products e expõe consultas/CRUD.

**Form Requests:** validam a entrada (payload) antes de chegar ao Controller.

**Migration:** define o schema (colunas/índices) no banco de dados.

**Routes (api.php):** mapeiam URL → método do Controller.

# Estrutura de pastas do projeto MVC

```text
app/
  Http/
    Controllers/
      ProductController.php
    Requests/
      ProductStoreRequest.php
      ProductUpdateRequest.php
  Models/
    Product.php
  Services/
    ProductService.php
database/
  migrations/
    2025_10_25_234940_create_products_table.php
routes/
  api.php
```
## Explicação dos elementos que compõem o código

1. Migration
```
**database/migrations/2025_10_25_000000_create_products_table.php**

Cria a tabela products com colunas:

id (chave primária)
name (string, com index para acelerar buscas por nome)
description (text, opcional)
price (decimal(10,2), default 0)
stock (integer, default 0)
code (string, unique)
timestamps (created_at/updated_at)
```

2. Model
```
**app/Models/Product.php**

Estende Model (Eloquent).

protected $fillable = [...] libera atribuição em massa (mass assignment) apenas dos campos permitidos.

Papel:

Representa o registro no banco (Active Record).

Fornece Query Builder Eloquent (ex.: Product::where(...)).
```

3. Requests
```
**app/Http/Requests/ProductStoreRequest.php** e **ProductUpdateRequest.php**

authorize() retorna true (sem ACL aqui, mas pode evoluir).

rules() define regras de validação:

no ProductStoreRequest.php campos obrigatórios com uso de required (name, price, stock, code).

no ProductUpdateRequest.php campos obrigatórios com uso de sometimes para permitir atualizações parciais.

Comportamento:

Em caso de erro de validação, o Laravel responde automaticamente com HTTP 422 + detalhes dos erros (JSON), antes do Controller executar.
```

4. Service
```
**app/Services/ProductService.php**

Métodos principais:

create(array $data): Product

findAll(?int $perPage = null): retorna lista (Collection) ou paginado (LengthAwarePaginator) conforme per_page.

findById(int $id): ?Product

findByName(string $name): Collection (usa LIKE e ordena por nome)

update(Product $product, array $data): Product

delete(Product $product): void

count(): int

Por que separar do Controller:

Mantém Controllers finos, facilita testes unitários e evolução de regras (ex.: descontos, políticas de estoque, etc.).

Centraliza a lógica de acesso ao repositório (Eloquent).
```

5. Controller
```
**app/Http/Controllers/ProductController.php**

Injeta ProductService via construtor (DI do Laravel).

Métodos e status codes:

store(ProductStoreRequest $req): 201 (criado) + JSON do produto.

index(Request $req): lista (ou pagina) produtos; lê per_page via $req->integer('per_page').

show(int $id): 200 com produto, ou 404 se não existir.

searchByName(Request $req): 200 com lista filtrada (?name=...).

update(ProductUpdateRequest $req, int $id): 200 com atualizado, ou 404.

destroy(int $id): 204 (sem corpo) se excluir, ou 404.

count(): 200 com {"count": n}.

Respostas sempre em JSON (via response()->json(...)).
```

6. Rotas
```
**routes/api.php**

Route::prefix('products')->group(...) organiza os endpoints:

GET /api/products → index (Find All, com ou sem paginação)

GET /api/products/count → count

GET /api/products/search?name=... → searchByName

GET /api/products/{id} → show (Find By ID)

POST /api/products → store (Create)

PUT /api/products/{id} → update

DELETE /api/products/{id} → destroy
```

## Fluxo Típico da requisição

1. Cliente chama POST /api/products com JSON.

2. Route direciona para ProductController@store.

3. ProductStoreRequest valida dados (se falhar → 422 automático).

4. Controller chama ProductService->create(...).

5. Service usa Product::create(...) (Model/Eloquent) para persistir.

6. Controller retorna 201 + produto criado em JSON.

Esse padrão se repete nos demais endpoints (listar, buscar, atualizar, excluir).

## Endpoints requeridos

**POST** /api/products – Create

**GET** /api/products – Find All

**GET** /api/products/{id} – Find By ID

**GET** /api/products/search?name=... – Find By Name

**PUT** /api/products/{id} – Update

**DELETE** /api/products/{id} – Delete

**GET** /api/products/count – Contagem total


