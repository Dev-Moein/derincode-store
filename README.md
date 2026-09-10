# 🛒 Derincode Store

<p align="center">

<img src="./screenshots/home.png" alt="Derincode Store Preview" width="900">

</p>


<p align="center">

A production-oriented full-stack digital marketplace built with Laravel and Vue.js, focusing on scalable architecture, clean backend design, and modern development practices.

</p>


<p align="center">

![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=flat&logo=laravel)
![Vue.js](https://img.shields.io/badge/Vue.js-3-42B883?style=flat&logo=vue.js)
![Docker](https://img.shields.io/badge/Docker-Containerized-2496ED?style=flat&logo=docker)
![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?style=flat&logo=mysql)

</p>


---

# 📌 About The Project

**Derincode Store** is a full-stack digital marketplace developed for managing and selling digital products.

The project is built with a focus on real-world software engineering concepts:

- Scalable backend architecture
- Clean code practices
- REST API design
- Maintainable business logic
- Containerized development workflow


The goal of this project is not only building an e-commerce system, but also applying professional development patterns used in production applications.


---

# 🌐 Demo

Coming soon...


---

# ✨ Features


## Backend

- Laravel REST API architecture
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
- Queue & Job processing
- Events & Listeners
- Email notifications


## Frontend

- Vue.js 3
- Composition API
- Pinia State Management
- Vue Router
- Axios API communication
- Responsive UI
- Component-based architecture
- Modern user experience


---

# 🏗 Architecture

The backend follows a structured architecture based on separation of concerns.


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


Implemented concepts:

- SOLID Principles
- Clean Code
- Separation of Concerns
- Dependency Injection
- Reusable Business Logic


---

# 🔌 API Overview

The backend provides RESTful APIs for:


### Authentication

- User registration
- Login
- Authorization


### Products

- Product listing
- Product details
- Product management


### Orders & Payments

- Purchase workflow
- Payment processing
- Order tracking


### User Management

- Profile management
- Purchase history


---

# 🗄 Database Design

Main entities:

```
Users

Products

Categories

Orders

Payments

Purchases

```

The database structure is designed to support scalable e-commerce workflows.


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
| Vue.js 3 | Frontend Framework |
| Pinia | State Management |
| Axios | API Communication |
| Vite | Build Tool |


---

# 🐳 Run With Docker


## Requirements

Before running the project:

- Docker
- Docker Compose
- Git


---

## Installation


Clone repository:

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


Docker will start:

```
Frontend Application

Backend API

MySQL Database

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
├── screenshots
│   └── home.png
│
├── docker-compose.yml
│
└── README.md
```


---

# 🔐 Security

Implemented security practices:


- Request validation
- Authentication protection
- Authorization rules
- Secure API responses
- Environment-based configuration
- Protected business operations


---

# 📸 Screenshots


## Home Page

<img src="./screenshots/home.png" alt="Home Page" width="900">


More screenshots will be added:

- Product details
- User dashboard
- Admin panel


---

# 🎯 Development Goals


This project was created to practice:


- Advanced Laravel development
- Vue.js application architecture
- Full-stack workflow
- REST API design
- Docker-based development
- Software architecture principles


---

# 🚀 Future Improvements


Planned improvements:

- Automated testing
- CI/CD pipeline
- Production deployment
- Advanced caching strategies
- More payment providers
- Monitoring and logging


---

# 👨‍💻 Author


## Moein

Full-Stack Developer


GitHub:

https://github.com/Dev-Moein


---

⭐ If you find this project useful, consider giving it a star.
