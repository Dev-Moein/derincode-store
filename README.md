# 🛒 Derincode Store

<p align="center">

<img src="./screenshots/home.png" alt="Derincode Store Preview" width="900">

</p>


<p align="center">
A modern full-stack digital marketplace built with Laravel and Vue.js.
</p>


---

# 📌 About The Project

**Derincode Store** is a full-stack e-commerce platform designed for selling digital products.

The project focuses on building a scalable and maintainable application using modern software engineering principles including clean architecture, service layers, API design, background processing, and containerized development.


---

# ✨ Features

## Backend

- Laravel REST API
- Authentication system
- Role-based authorization
- Product management
- Category management
- Purchase workflow
- Payment system
- Order management
- API Resources
- Form Request validation
- Service Layer architecture
- Repository Pattern
- Dependency Injection
- Database transactions
- Queue & Jobs
- Events & Listeners
- Email notifications


## Frontend

- Vue.js 3
- Composition API
- Pinia State Management
- Vue Router
- Axios API integration
- Responsive interface
- Component-based structure
- Modern UI design


---

# 🏗 Architecture

The backend structure is designed with separation of concerns:

```
backend

app
│
├── Http
│   ├── Controllers
│   ├── Requests
│   └── Resources
│
├── Services
│
├── Repositories
│
├── Interfaces
│
├── Jobs
│
├── Events
│
└── Listeners
```


Main principles:

- SOLID Principles
- Clean Code
- Maintainable Business Logic
- Scalable Architecture


---

# 🛠 Tech Stack


## Backend

| Technology | Purpose |
|---|---|
| Laravel | Backend Framework |
| PHP | Programming Language |
| MySQL | Database |
| Redis | Queue & Cache |
| Docker | Containerization |


## Frontend

| Technology | Purpose |
|---|---|
| Vue.js | Frontend Framework |
| Pinia | State Management |
| Axios | API Communication |
| Vite | Build Tool |


---

# 🐳 Run With Docker


## Requirements

Before running the project make sure you have:

- Docker
- Docker Compose
- Git


---

## Installation


Clone the repository:

```bash
git clone https://github.com/Dev-Moein/derincode-store.git

cd derincode-store
```


Create environment files:

Backend:

```bash
cp backend/.env.example backend/.env
```


Frontend:

```bash
cp frontend/.env.example frontend/.env
```


Build and start containers:


```bash
docker compose up -d --build
```


The following services will start automatically:

```
Frontend
Backend API
Database
Redis
Queue Worker
```

---

# 📂 Project Structure


```
derincode-store

│
├── backend
│   ├── app
│   ├── routes
│   ├── database
│   └── Dockerfile
│
├── frontend
│   ├── src
│   ├── components
│   ├── stores
│   └── Dockerfile
│
├── docker-compose.yml
│
├── screenshots
│   └── home.png
│
└── README.md
```


---

# 🔐 Security

Implemented:

- Request validation
- Authentication protection
- Authorization rules
- Secure API responses
- Environment based configuration


---

# 🎯 Development Purpose

This project was created to practice real-world full-stack development:

- Laravel advanced features
- Vue.js application architecture
- REST API design
- Docker workflow
- Software architecture concepts


---

# 👨‍💻 Author


## Moein

Full-Stack Developer


GitHub:

https://github.com/Dev-Moein


---

⭐ If you like this project, consider giving it a star.
