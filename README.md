# Elipse

Sistema de gestão de clientes, jobs, pedidos e cadastros. O backend é Laravel e a interface é Vue, ligada ao servidor pelo Inertia.

O ambiente local sobe com Docker. PHP, Node, MySQL e Redis rodam em containers.

## O que é preciso para instalar

- [Docker](https://docs.docker.com/get-docker/)
- [Docker Compose](https://docs.docker.com/compose/)
- Git

Não é necessário instalar PHP, Composer, Node ou MySQL na máquina. O `docker compose` cuida disso.

## Instalação

Na pasta do projeto:

```bash
cp .env.example .env
```

Ajuste o `.env` para falar com os containers. Os valores abaixo batem com o `docker-compose.yml`:

```env
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=elipse_db
DB_USERNAME=elipseERP
DB_PASSWORD=elipseERP

REDIS_HOST=redis
REDIS_PORT=6379
```

Suba os containers. Na primeira vez a imagem é construída, as dependências são instaladas e as migrations rodam sozinhas. Isso pode levar alguns minutos.

```bash
docker compose up -d --build
```

Crie o usuário inicial e os dados básicos (departamentos, status e unidades de serviço):

```bash
docker compose exec app php artisan db:seed
```

## Rodar localmente

| Serviço | Onde acessar |
| --- | --- |
| Aplicação | http://localhost:8000 |
| Vite (assets e recarregamento da tela) | http://localhost:5173 |
| MySQL | `localhost:3306` |
| Redis | `localhost:6379` |

Comandos do dia a dia, sempre a partir da pasta do projeto:

```bash
docker compose up -d          # sobe os containers
docker compose down           # para os containers
docker compose logs -f app    # acompanha o log da aplicação
docker compose exec app php artisan migrate
```

A fila de auditoria usa o canal `audit` (veja `config/audit.php`). Em desenvolvimento, o worker não sobe junto com a aplicação. Rode em um terminal separado:

```bash
docker compose exec app php artisan queue:work --queue=audit,default
```

## Usuário para entrar na aplicação

O seeder `UserAdminSeeder` cria um administrador:

| Campo | Valor |
| --- | --- |
| E-mail | `admin@example.com` |
| Senha | `password` |
| Perfil | admin |

Acesse http://localhost:8000 e entre com esses dados. Esse usuário existe só depois do `db:seed`.

## Arquitetura

O código segue Clean Architecture. A regra de negócio fica no domínio e não conhece o Laravel. Quem precisa gravar ou ler dados depende de uma interface. A implementação (Eloquent, arquivo, hash de senha) fica na infraestrutura e é ligada no container em `app/Providers/RepositoryServiceProvider.php`.

| Camada | Pasta | Responsabilidade |
| --- | --- | --- |
| Domain | `app/Domain` | Entidades, value objects, enums, exceções e contratos de persistência |
| UseCase | `app/UseCase` | Uma classe por operação de negócio, com DTOs de entrada e saída |
| Infrastructure | `app/Infrastructure` | Eloquent, arquivos e serviços que falam com o mundo externo |
| Http | `app/Http` | Controllers, Form Requests e validação da requisição |

A tela é Vue 3 em `resources/js`, renderizada pelo Inertia. O controller monta o DTO, chama o caso de uso e devolve a página. A regra (CNPJ, código de job, comissão, e-mail) mora no domínio, não no componente.

## Testes

Os testes são PHPUnit 11 e rodam dentro do container `app`. Usam SQLite em memória, então não alteram o banco MySQL de desenvolvimento.

Toda a suíte:

```bash
docker compose exec app php artisan test
```

Cada parte:

```bash
docker compose exec app php artisan test --testsuite=Unit
docker compose exec app php artisan test --testsuite=Integration
docker compose exec app php artisan test --testsuite=EndToEnd
```

| Suíte | Pasta | O que cobre |
| --- | --- | --- |
| Unit | `tests/Unit` | Domínio, value objects e casos de uso isolados |
| Integration | `tests/Integration` | Repositórios Eloquent, casos de uso com banco, HTTP e auditoria |
| EndToEnd | `tests/EndToEnd` | Suíte reservada no `phpunit.xml` para fluxos de ponta a ponta |

Para um arquivo ou um método:

```bash
docker compose exec app php artisan test --filter=NomeDoTeste
```

Não há suíte de testes de frontend. Vue e Inertia são exercitados pelos testes HTTP quando o fluxo passa pelo controller.

## Tecnologias

**Backend**

- PHP 8.4
- Laravel 12
- Inertia Laravel 2
- Ziggy (rotas nomeadas no JavaScript)
- PHPUnit 11

**Frontend**

- Vue 3
- Inertia.js
- Vite 7
- Tailwind CSS 4
- Font Awesome

**Infraestrutura local**

- Docker Compose
- Nginx e PHP-FPM
- MySQL 8
- Redis 7
