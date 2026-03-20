# 🏨 API de Gestão Hoteleira

API REST para gerenciamento de hotéis, quartos e reservas, desenvolvida com PHP 8 + Laravel.

---

## 🚀 Como Iniciar o Projeto

### 1. Clone o repositório

```bash
git clone https://github.com/Samuel-Araujo-Silveira/api-foco.git
cd api-foco
```

### 2. Instale as dependências

```bash
composer install
```

### 3. Configure o ambiente

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Execute as migrations

```bash
php artisan migrate
```

### 5. Crie o usuário administrador

```bash
php artisan tinker --execute="App\Models\User::factory()->create(['email' => 'admin@teste.com', 'password' => bcrypt('12345')]);"
```

### 6. Inicie o servidor

```bash
php artisan serve
```

---

## 🔐 Autenticação

A rota de importação de XMLs é protegida por autenticação via **Laravel Sanctum**.

### Realizando login

**Rota:** `POST {seuLocalhost}/api/v1/login`

**Header:**
| Key | Value |
|---|---|
| Content-Type | application/json |

**Body:**
```json
{
    "email": "admin@teste.com",
    "password": "12345"
}
```

**Resposta:**
```json
{
 "message": "Autenticado com sucesso",
    "status": 200,
    "data": {
        "token": "{token}"
    }
}
```

> Copie o token retornado — ele será necessário para acessar a rota de importação.

---

## 📡 Acessando as Rotas

### 🔒 POST `/api/v1/import` — Importação de XMLs
> **Rota protegida — requer autenticação**

Importa os dados dos arquivos `hotels.xml`, `rooms.xml`, `rates.xml` e `reservations.xml`.

**Header:**
| Key | Value |
|---|---|
| Content-Type | application/json |
| Authorization | Bearer {token} |

**Resposta de sucesso:** `200 OK`
```json
{
    "message": "Importação realizada com sucesso"
}
```

---

### 🏠 Quartos — `/api/v1/rooms`

#### GET `/api/v1/rooms` — Listar todos os quartos

**Resposta de sucesso:** `200 OK`
```json
[
    {
        "id": 137598802,
        "hotel_id": 1375988,
        "name": "Deluxe Double Room",
        "inventory_count": 15
    }
]
```

---

#### GET `/api/v1/rooms/{id}` — Exibir um quarto

**Resposta de sucesso:** `200 OK`
```json
{
    "id": 137598802,
    "hotel_id": 1375988,
    "name": "Deluxe Double Room",
    "inventory_count": 15
}
```

**Resposta de erro:** `404 Not Found`

---

#### POST `/api/v1/rooms` — Criar um quarto

**Header:**
| Key | Value |
|---|---|
| Content-Type | application/json |

**Body:**
```json
{
    "id": 999,
    "hotel_id": 1375988,
    "name": "Quarto Deluxe",
    "inventory_count": 10
}
```

**Resposta de sucesso:** `200 OK`

---

#### PATCH `/api/v1/rooms/{id}` — Atualizar um quarto

**Body:**
```json
{
    "name": "Quarto Master",
    "inventory_count": 5
}
```

**Resposta de sucesso:** `200 OK`

---

#### DELETE `/api/v1/rooms/{id}` — Remover um quarto

**Resposta de sucesso:** `200 OK`
```json
{
    "message": "Room deleted"
}
```

---

### 📋 Reservas — `/api/v1/reservations`

#### GET `/api/v1/reservations` — Listar todas as reservas

**Resposta de sucesso:** `200 OK`
```json
[
    {
        "id": 3820212524,
        "date": "2026-03-16",
        "time": "09:15:00",
        "hotel_id": 1375988,
        "customer": {
            "first_name": "Bruno",
            "last_name": "Nascimento"
        },
        "room": {
            "id": 3641632087,
            "arrival_date": "2026-04-10",
            "departure_date": "2026-04-12",
            "currencycode": "BRL",
            "meal_plan": "Breakfast included.",
            "totalprice": 300.00,
            "guest_counts": [
                { "type": "adult", "count": 2 }
            ],
            "prices": [
                { "rate_id": 5333849, "date": "2026-04-10", "amount": 150.00 }
            ]
        }
    }
]
```

---

#### GET `/api/v1/reservations/{id}` — Exibir uma reserva

**Resposta de sucesso:** `200 OK`

**Resposta de erro:** `404 Not Found`

---

#### POST `/api/v1/reservations` — Criar uma reserva

> O sistema valida automaticamente a disponibilidade do quarto no período solicitado.

**Header:**
| Key | Value |
|---|---|
| Content-Type | application/json |

**Body:**
```json
{
    "id": 9999999999,
    "reservation_room_id": 8888888888,
    "first_name": "João",
    "last_name": "Silva",
    "hotel_id": 1375988,
    "room_id": 137598802,
    "arrival_date": "2026-06-01",
    "departure_date": "2026-06-05",
    "currencycode": "BRL",
    "meal_plan": "Breakfast included.",
    "totalprice": 300.00,
    "guest_counts": [
        { "type": "adult", "count": 2 }
    ],
    "prices": [
        { "rate_id": 5333849, "date": "2026-06-01", "amount": 150.00 },
        { "rate_id": 5333849, "date": "2026-06-02", "amount": 150.00 }
    ]
}
```

**Resposta de sucesso:** `201 Created`

**Resposta de conflito:** `409 Conflict`
```json
{
    "message": "Room not available for this period"
}
```

---

#### PATCH `/api/v1/reservations/{id}` — Atualizar uma reserva

**Body:**
```json
{
    "arrival_date": "2026-06-10",
    "departure_date": "2026-06-15",
    "meal_plan": "No meals.",
    "totalprice": 500.00,
    "guest_counts": [
        { "type": "adult", "count": 1 }
    ],
    "prices": [
        { "rate_id": 5333849, "date": "2026-06-10", "amount": 250.00 }
    ]
}
```

**Resposta de sucesso:** `200 OK`

---

#### DELETE `/api/v1/reservations/{id}` — Cancelar uma reserva

**Resposta de sucesso:** `200 OK`
```json
{
    "message": "Reservation deleted"
}
```

**Resposta de erro:** `404 Not Found`

---

## 🧪 Executando os Testes

```bash
php artisan test
```

**Resultado esperado:**
```
PASS  Tests\Unit\AvailabilityLogicTest
PASS  Tests\Feature\ReservationServiceTest
PASS  Tests\Feature\ReservationTest
PASS  Tests\Feature\Api\V1\ReservationApiTest
Tests: 19 passed
```

---

## 📋 Requisitos do Projeto

### 1. Importação e CRUD

A importação dos dados é realizada pelo `XmlImportService`, localizado em `app/Services/XmlImportService.php`. O serviço lê os arquivos `hotels.xml`, `rooms.xml`, `rates.xml` e `reservations.xml` localizados em `storage/app/private/xml/` e os persiste no banco de dados.

Para arquivos de estrutura simples (`hotels.xml`, `rooms.xml`, `rates.xml`), é utilizado o método genérico `importXml()`, que percorre os nós com `XMLReader` e persiste via `updateOrCreate`. Para o `reservations.xml`, de estrutura aninhada mais complexa, foi criado o método dedicado `importReservations()`, que combina `XMLReader` para navegação e `SimpleXML` para leitura interna de cada reserva.

O CRUD de quartos é exposto via `RoomController` com as rotas `GET`, `POST`, `PATCH` e `DELETE` em `/api/v1/rooms`.
O CRUD de reservations é exposto via `ReservationsController` com as rotas `GET`, `POST`, `PATCH` e `DELETE` em `/api/v1/reservations`.

---

### 2. Regra de Disponibilidade

A regra de disponibilidade está implementada no `ReservationService`, em `app/Services/ReservationService.php`, pelo método `isRoomAvailable()`.

O método consulta todas as `ReservationRooms` vinculadas ao quarto solicitado e verifica conflito de datas usando o método `hasConflict()`, que compara as datas com Carbon:

```php
return $newIn->lt($exOut) && $newOut->gt($exIn);
```

Se houver conflito, a API retorna `409 Conflict` e a reserva não é criada.

---

### 3. Arquitetura

O projeto foi construído seguindo os seguintes padrões de projeto:

**Service Layer** — toda a lógica de negócio está separada dos Controllers:
- `app/Services/ReservationService.php` — criação, atualização, deleção e disponibilidade de reservas
- `app/Services/XmlImportService.php` — importação dos arquivos XML

**Repository Pattern** — abstração do acesso ao banco de dados:
- `app/Repositories/Contracts/RoomRepositoryInterface.php`
- `app/Repositories/Contracts/ReservationRepositoryInterface.php`
- `app/Repositories/Eloquent/RoomRepository.php`
- `app/Repositories/Eloquent/ReservationRepository.php`

**Form Requests** — validação isolada dos Controllers:
- `app/Http/Requests/StoreRoomRequest.php`
- `app/Http/Requests/UpdateRoomRequest.php`
- `app/Http/Requests/StoreReservationRequest.php`
- `app/Http/Requests/UpdateReservationRequest.php`

**API Resources** — formatação padronizada das respostas:
- `app/Http/Resources/V1/ReservationResource.php`

**Traits** — comportamentos reutilizáveis:
- `app/Traits/HttpResponses.php` — padronização das respostas HTTP da API

---

### 4. Testes

O projeto possui testes unitários e de integração, executados com PHPUnit.

#### `Tests\Unit\AvailabilityLogicTest`
Teste unitário puro — sem banco de dados, sem factories. Testa diretamente o método `hasConflict()` do `ReservationService`, validando a lógica de comparação de datas:

| Método | Objetivo |
|---|---|
| `test_dates_overlap` | Verifica que datas sobrepostas são detectadas como conflito |
| `test_dates_do_not_overlap` | Verifica que datas adjacentes não geram conflito |
| `test_dates_completely_before` | Verifica que datas anteriores ao período não conflitam |
| `test_dates_completely_after` | Verifica que datas posteriores ao período não conflitam |

#### `Tests\Feature\ReservationServiceTest`
Testa o método `isRoomAvailable()` do `ReservationService` com banco de dados em memória:

| Método | Objetivo |
|---|---|
| `test_room_is_available_when_no_reservations_exist` | Quarto sem reservas deve estar disponível |
| `test_room_is_not_available_when_dates_overlap` | Quarto com reserva conflitante deve ser bloqueado |
| `test_room_is_available_when_dates_do_not_overlap` | Quarto com reserva em outro período deve estar disponível |

#### `Tests\Feature\ReservationTest`
Testa o fluxo completo de criação de reservas via HTTP:

| Método | Objetivo |
|---|---|
| `test_can_create_reservation` | Reserva válida deve ser criada com sucesso (`201`) |
| `test_cannot_create_reservation_when_room_is_unavailable` | Reserva em período ocupado deve retornar `409` |
| `test_cannot_create_reservation_with_invalid_data` | Payload inválido deve retornar `422` |

#### `Tests\Feature\Api\V1\ReservationApiTest`
Testa todos os endpoints da API de reservas de forma abrangente:

| Método | Objetivo |
|---|---|
| `test_index_returns_all_reservations` | Listagem retorna todas as reservas |
| `test_index_returns_empty_collection_when_no_reservations` | Listagem vazia retorna array vazio |
| `test_show_returns_reservation` | Exibição retorna reserva correta |
| `test_show_returns_404_when_reservation_not_found` | ID inexistente retorna `404` |
| `test_store_creates_reservation_successfully` | Criação bem-sucedida retorna `201` |
| `test_store_returns_409_when_room_is_not_available` | Conflito de datas retorna `409` |
| `test_store_returns_422_when_payload_is_invalid` | Dados inválidos retornam `422` |
| `test_destroy_deletes_reservation_successfully` | Deleção bem-sucedida retorna `200` |
| `test_destroy_returns_404_when_reservation_not_found` | ID inexistente retorna `404` |

---

## ⭐ Extras

### Diagrama de Classes

O diagrama de classes do projeto está disponível na raiz do repositório no arquivo `diagrama-classes.png`, ilustrando os relacionamentos entre as entidades `Hotel`, `Room`, `Rate`, `Reservation`, `ReservationRoom`, `Customer`, `GuestCount` e `RateReservationRoom`.

<img width="679" height="407" alt="Captura de tela de 2026-03-20 01-55-29" src="https://github.com/user-attachments/assets/3c17ac0f-0aaa-4505-984a-b3efba8132bb" />


---

### Swagger

A documentação interativa da API foi gerada com o pacote `darkaonline/l5-swagger` e está disponível com o servidor rodando em:

```
http://127.0.0.1:8000/api/documentation
```

Todos os endpoints estão documentados com seus parâmetros, bodies e respostas esperadas, permitindo testar a API diretamente pelo navegador.

---

### Log

O sistema utiliza o `Log` Facade do Laravel para registrar eventos importantes em `storage/logs/laravel.log`:

| Nível | Evento |
|---|---|
| `info` | Reserva criada, atualizada ou deletada |
| `info` | Início e fim da importação de XMLs |
| `warning` | Tentativa de reserva em quarto indisponível |
| `error` | Exceções capturadas nos Controllers |

Para acompanhar os logs em tempo real:

```bash
tail -f storage/logs/laravel.log
```

---

## 📐 Arquitetura

O projeto segue os seguintes padrões de projeto:

- **Service Layer** — lógica de negócio separada dos Controllers (`ReservationService`, `XmlImportService`)
- **Repository Pattern** — abstração do acesso a dados em `RoomRepository` e `ReservationRepository`
- **Form Requests** — validação de dados isolada dos Controllers
- **API Resources** — formatação de respostas padronizada

### Decisões Técnicas

- O **Repository Pattern** foi aplicado nos Controllers de Rooms e Reservations, onde o fluxo de negócio justifica a abstração.
- O **XmlImportService** mantém acesso direto ao Eloquent por ser uma operação de carga de dados inicial, sem necessidade de abstração adicional.
- A rota de importação é a única protegida por autenticação, pois é uma operação administrativa sensível.

---

## 📚 Documentação Swagger

Com o servidor rodando, acesse:

```
http://127.0.0.1:8000/api/documentation
```

---

## 🛠️ Tecnologias

- PHP 8.4
- Laravel 12
- SQLite
- PHPUnit
- Laravel Sanctum
- L5-Swagger
