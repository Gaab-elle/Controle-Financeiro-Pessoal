# TôNoAzul

Sistema completo de controle financeiro pessoal desenvolvido com Laravel (backend) e Vue.js (frontend).

## Funcionalidades

- Autenticação de usuários (Login/Registro)
- Cadastro de contas (Banco, Carteira, Cartão)
- Registro de lançamentos (Entradas e Saídas)
- Dashboard com resumo financeiro
- Cálculo automático de saldos
- Interface moderna e responsiva

## Tecnologias

### Backend
- Laravel 11
- Laravel Sanctum (Autenticação)
- MySQL
- PHP 8.2+

### Frontend
- Vue.js 3 (Composition API)
- Vue Router
- Pinia (State Management)
- Axios
- TailwindCSS
- Vite

## Instalação

### Pré-requisitos
- PHP 8.2 ou superior
- Composer
- Node.js 18+ e npm
- MySQL
- Git

### Backend (Laravel)

1. Navegue até a pasta do backend:
```bash
cd backend
```

2. Instale as dependências:
```bash
composer install
```

3. Copie o arquivo de ambiente:
```bash
cp .env.example .env
```

4. Gere a chave da aplicação:
```bash
php artisan key:generate
```

5. Configure o banco de dados no arquivo `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=controle_financeiro
DB_USERNAME=root
DB_PASSWORD=sua_senha
```

6. Execute as migrations:
```bash
php artisan migrate
```

7. Inicie o servidor:
```bash
php artisan serve
```

O backend estará disponível em `http://localhost:8000`

### Frontend (Vue.js)

1. Navegue até a pasta do frontend:
```bash
cd frontend
```

2. Instale as dependências:
```bash
npm install
```

3. Inicie o servidor de desenvolvimento:
```bash
npm run dev
```

O frontend estará disponível em `http://localhost:5173`

## Configuração da Autenticação

A aplicação usa Laravel Sanctum para autenticação. Certifique-se de que as seguintes configurações estão corretas:

### Backend (.env)
```env
SANCTUM_STATEFUL_DOMAINS=localhost:5173,localhost:3000,127.0.0.1:5173,127.0.0.1:3000
SESSION_DOMAIN=localhost
```

### CORS
O CORS já está configurado para permitir requisições do frontend. Verifique o arquivo `backend/config/cors.php`.

## Estrutura da API

### Autenticação
- `POST /api/register` - Registrar novo usuário
- `POST /api/login` - Login
- `POST /api/logout` - Logout
- `GET /api/me` - Obter usuário autenticado

### Contas
- `GET /api/contas` - Listar contas
- `POST /api/contas` - Criar conta
- `GET /api/contas/{id}` - Obter conta
- `PUT /api/contas/{id}` - Atualizar conta
- `DELETE /api/contas/{id}` - Excluir conta

### Lançamentos
- `GET /api/lancamentos` - Listar lançamentos
- `POST /api/lancamentos` - Criar lançamento
- `GET /api/lancamentos/{id}` - Obter lançamento
- `PUT /api/lancamentos/{id}` - Atualizar lançamento
- `DELETE /api/lancamentos/{id}` - Excluir lançamento

### Dashboard
- `GET /api/dashboard` - Obter resumo financeiro

## Regras de Negócio

### Contas
- Cada conta pertence a um usuário
- Tipos disponíveis: `banco`, `carteira`, `cartao`
- Saldo inicial é opcional (padrão: 0)
- Saldo atual é calculado automaticamente baseado nos lançamentos

### Lançamentos
- Cada lançamento pertence a uma conta e um usuário
- Tipos disponíveis: `entrada`, `saida`
- Entradas aumentam o saldo da conta
- Saídas diminuem o saldo da conta
- Valor mínimo: R$ 0,01

## Desenvolvimento

### Backend
```bash
cd backend
php artisan serve
```

### Frontend
```bash
cd frontend
npm run dev
```

## Licença

Este projeto é open source e está disponível sob a licença MIT.

