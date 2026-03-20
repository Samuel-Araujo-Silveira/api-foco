# 🏨 API de Gestão Hoteleira

API REST para gerenciamento de hotéis, quartos e reservas, desenvolvida com PHP 8 + Laravel.

---

## 🚀 Como Iniciar o Projeto

### 1. Clone o repositório

```bash
git clone https://github.com/seu-usuario/seu-repositorio.git
cd seu-repositorio
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

### 5. Inicie o servidor

```bash
php artisan serve
```

### 6. Crie o usuário administrador

```bash
php artisan tinker --execute="App\Models\User::factory()->create(['email' => 'admin@teste.com', 'password' => bcrypt('12345')]);"
```

---

## 🔐 Autenticação

A rota de importação de XMLs é protegida por autenticação via **Laravel Sanctum**.

### Realizando login

**Rota:** `POST 127.0.0.1:8000/api/v1/login`

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
    "token": "seu-token-aqui"
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
