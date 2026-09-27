# laminas-api-sample

![CI](https://github.com/blakesimpson-dev/laminas-api-sample/actions/workflows/ci.yml/badge.svg)

A learning project using PHP 8.5, Laminas and Doctrine: a small REST backend
modelled on a slice of the
[Path of Exile developer API](https://www.pathofexile.com/developer/docs/reference).

Whilst paths, response shapes and error codes all follow the published docs, the
API serves its own data and does not call GGG's API.

The API is hand-wired from Laminas components, rather than a full framework or
starter template. This way each part can be understood, explained, and reasoned
for. The commit history follows that progression.

![Demo](docs/demo.gif)

## Features

- `GET /profile`, `GET|POST /item-filter`, `GET|POST /item-filter/{id}`, with
  documented response and error shapes
- Mocked OAuth 2.1 bearer authentication - route scopes, expire and revoke for
  tokens and per-profile ownership for item filters
- OpenAPI 3.1 spec (`docs/openapi.json`) with Swagger UI available at `/docs`. A
  contract test keeps the model synchronized.
- Create and partial update with validation - actions are recorded as timestamps
  on each entity via an injected system clock
- Doctrine entities, embeddables, and reviewed migrations
- Nginx + php-fpm + PostgreSQL + Swagger UI in Docker Compose, credentials in
  shared environment from `.env`
- Fixtures seeded from real data
- PHPUnit testing for entities, input validation, adapters and the HTTP layer
- Written with adherence to modern PHP conventions using strict static analysis

## Auth

This replication only covers the resource server (`api.pathofexile.com`). Token
issuance (`www.pathofexil.com/oauth`) is out of scope. Development tokens are
seeded as if the authorization server had isued them, and stored only as SHA-256
hashes. Like the real API, the token identifies it's owner, so `account:*`
endpoints do not need an `id` in the path. 401 and 403 responses carry
`WWW-Authenticate` headers (RFC 6750).

| Endpoint                                                | Scope                 |
| ------------------------------------------------------- | --------------------- |
| `GET /profile`                                          | `account:profile`     |
| `GET\|POST /item-filter`, `GET\|POST /item-filter/{id}` | `account:item_filter` |

| Dev token                 | Demonstrates                                    |
| ------------------------- | ----------------------------------------------- |
| `dev-token-full`          | Full access for `ProfileOne`                    |
| `dev-token-profile-only`  | 403 (code 6) on `/item-filter`: missing scope   |
| `dev-token-expired`       | 401 (code 8): expired                           |
| `dev-token-revoked`       | 401 (code 8): revoked                           |
| `dev-token-other-profile` | `ProfileTwo`. (`ProfileOne` filters return 404) |

```bash
# 200: Ok
curl -i -H 'Authorization: Bearer dev-token-full' localhost:8000/profile

# 403: Forbidden
curl -i -H 'Authorization: Bearer dev-token-profile-only' localhost:8000/item-filter
```

## How it works

```mermaid
flowchart TD
    C[Client] --> |request| N[Nginx :8000<br/>or composer serve :8080]
    N --> I[public/index.php]
    I --> R[Router<br/>route + method]
    R --> Au[BearerAuthenticator<br/>token + scope]
    Au --> H[Handler]
    H --> V[JsonBody + InputFilter<br/>parse + validate]
    V --> Rp[Repository]
    Rp --> A[Adapter<br/>entity → documented shape]
    A --> J[JsonResponseFactory]
    J --> |response| C

    Au -.->|token lookup| D[(PostgreSQL)]
    Rp -.->|read / write| D
```

## Build and run

Requires Docker and Composer. PHP 8.5 with `pdo_pgsql` is needed locally for
Composer scripts, tests and `composer serve`.

```bash
git clone https://github.com/blakesimpson-dev/laminas-api-sample.git
cd laminas-api-sample
composer install
cp .env.example .env                                # local dev defaults, adjust if needed
composer reset                                      # fresh Postgres + migrations
composer fixtures:load                              # seeding
```

### Run via Nginx (prod style)

```bash
composer up                                         # Postgres, php-fpm, Nginx on :8000
export AUTH='Authorization: Bearer dev-token-full'
curl -H "$AUTH" localhost:8000/profile              # get account profile
curl -H "$AUTH" localhost:8000/item-filter          # list item filters
curl -H "$AUTH" localhost:8000/item-filter/<id>     # get one item filter
```

Nginx serves `public/`, passes `index.php` to php-fpm over FastCGI and returns
404 for any other `.php` path. JSON responses are gzipped and server and PHP
versions are stripped from headers.

### Run via built-in server

```bash
composer serve                                       # http://localhost:8080
curl -H "$AUTH" localhost:8080/profile
```

Both can run at the same time against the same database.

### API docs

The API contract is a hand-written OpenAPI 3.1 spec, `docs/openapi.json`, served
through Nginx with Swagger UI:

```bash
composer up                                          # then open http://localhost:8000/docs
```

## Scripts

| Script                                            | Purpose                                                 |
| ------------------------------------------------- | ------------------------------------------------------- |
| `composer serve`                                  | PHP built-in server on port 8080                        |
| `composer migrations:diff` / `migrations:migrate` | Doctrine Migrations                                     |
| `composer fixtures:load`                          | Purge and reseed                                        |
| `composer reset`                                  | Recreate Postgres and migrate                           |
| `composer test`                                   | Run the PHPUnit unit tests                              |
| `composer up`                                     | Build and start Postgres, php-fpm and Nginx (port 8000) |
| `composer down`                                   | Stop the Docker services, volume is kept                |
| `composer lint` / `analyze`                       | Mago linter and static analyzer (fail on warnings)      |
| `composer format` / `format:check`                | Mago formatter (PER-CS based)                           |

## Layout

| Path                 | Purpose                                                                  |
| -------------------- | ------------------------------------------------------------------------ |
| `config`             | Routes, service manager, Doctrine                                        |
| `src/Http`           | Router, JSON body parsing, error responses                               |
| `src/Domains`        | One folder per domain: entity, repository, adapter, handlers, validation |
| `src/Infrastructure` | Doctrine entity manager factory, system clock                            |
| `docker`             | php-fpm image and Nginx site config                                      |
| `migrations`         | Reviewed Doctrine migrations                                             |
| `test`               | Unit tests, fixtures and fixture data                                    |

## Roadmap

- Lightweight Vite + Vue 3 + TypeScript web client, with Pinia and SASS styling
- Per-token rate limiting in Redis, with the documented headers

## Credits

Item filter fixtures are
[NeverSink's filters](https://github.com/NeverSinkDev/NeverSink-Filter) (MIT),
exported from FilterBlade.

The application code is my own (MIT, see [LICENSE](LICENSE)). An LLM was used
for explanations and tooling configuration.

This project isn't affiliated with or endorsed by Grinding Gear Games in any
way.

## References

- [Path of Exile developer docs](https://www.pathofexile.com/developer/docs)
- [Laminas](https://docs.laminas.dev/)
- [Doctrine ORM](https://www.doctrine-project.org/projects/orm.html),
  [Migrations](https://www.doctrine-project.org/projects/migrations.html),
  [Data Fixtures](https://www.doctrine-project.org/projects/data-fixtures.html)
- [OpenAPI 3.1](https://spec.openapis.org/oas/v3.1.0),
  [Swagger UI](https://swagger.io/tools/swagger-ui/),
  [Redocly CLI](https://redocly.com/docs/cli/)
- [Mago](https://mago.carthage.software/)
