# Derincode Store

> A full-stack marketplace project built to practice production-oriented backend engineering with Laravel and Vue.js.

![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=flat&logo=laravel&logoColor=white)
![Vue.js](https://img.shields.io/badge/Vue.js-3-42B883?style=flat&logo=vue.js&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?style=flat&logo=mysql&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-Containerized-2496ED?style=flat&logo=docker&logoColor=white)

## Overview

Derincode Store is a full-stack digital marketplace built around a Laravel backend and Vue.js frontend.

The project is used as an engineering-focused portfolio piece, with emphasis on:

- REST API design
- Authentication and authorization
- Business logic separation
- Database transactions
- Service and repository layers
- Background jobs
- Containerized development
- Maintainable frontend/backend boundaries

## Architecture

```text
derincode-store/
├── backend/
│   ├── app/
│   ├── routes/
│   ├── database/
│   └── Dockerfile
├── frontend/
│   ├── src/
│   ├── components/
│   ├── stores/
│   └── Dockerfile
├── screenshots/
└── docker-compose.yml
```

## Core Areas

### Backend
- Laravel REST API
- Authentication & authorization
- Product and category management
- Order and purchase workflows
- Payment handling
- Request validation
- API Resources
- Service layer
- Repository layer
- Database transactions
- Queue/job processing

### Frontend
- Vue.js 3
- Composition API
- Pinia
- Vue Router
- Axios
- Component-based UI

## Local Development

### Requirements

- Git
- Docker
- Docker Compose

### Setup

```bash
git clone https://github.com/Dev-Moein/derincode-store.git
cd derincode-store

cp backend/.env.example backend/.env
cp frontend/.env.example frontend/.env

docker compose up -d --build
```

Check the repository environment files for project-specific configuration before starting the application.

## Screenshots

<p align="center">
  <img src="./screenshots/home.png" width="900" alt="Derincode Store home page">
</p>

## Engineering Goals

This project is intentionally used to practice:

- Clean Code
- SOLID
- Separation of Concerns
- Dependency Injection
- REST API design
- Docker-based development
- Backend/frontend separation

## Roadmap

- Automated test coverage
- CI/CD
- Production deployment
- Observability and logging
- Further performance and caching work

## Author

**Moein — Backend Software Engineer**

[GitHub](https://github.com/Dev-Moein)
