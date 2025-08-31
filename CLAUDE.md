# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with this Twitter clone project.

## Initial Setup

### Development Environment
1. **Copy environment file**: 
   ```bash
   cp .env.example .env
   ```
   Configure database, Redis, and Pusher settings as needed.

2. **Generate app key**:
   ```bash
   php artisan key:generate
   ```

3. **Create storage link**:
   ```bash
   php artisan storage:link
   ```

4. **Install Horizon**:
   ```bash
   php artisan horizon:install
   php artisan horizon:publish
   ```

5. **Setup Passport**:
   ```bash
   php artisan passport:keys
   php artisan passport:client --personal --no-interaction
   ```

## Commands

### Backend (Laravel)
- **Dependencies & Setup**:
  ```bash
  composer install
  php artisan key:generate
  php artisan storage:link
  ```

- **Database & Testing**:
  ```bash
  php artisan migrate
  vendor/bin/phpunit  # All tests
  vendor/bin/phpunit tests/Feature/PostTest.php  # Single test
  ```

- **Real-time & Maintenance**:
  ```bash
  php artisan horizon  # Queue worker
  vendor/bin/pint     # Linting
  ```

### Frontend (Vue.js)
- **Install dependencies**:
  ```bash
  npm install
  ```
- **Development server**:
  ```bash
  npm run dev
  ```
- **Build for production**:
  ```bash
  npm run build
  ```
- **Vite with HMR**:
  ```bash
  npm run dev -- --host
  ```

## Architecture Overview

### Backend (Laravel 12)
- **API Structure**:
  - RESTful endpoints prefixed with `/api/v1`
  - Validation via FormRequest classes
  - Business logic decoupled in `app/Services/`
- **Real-time Events**:
  - WebSocket channels via Laravel Echo
  - Events defined in `app/Events/`
- **Authentication**:
  - OAuth2 via Laravel Passport
  - Policies for granular authorization

### Frontend (Vue.js 3)
- **State Management**:
  - Vuex store handles auth and WebSocket connections
  - API client (Axios) with interceptors
- **UI Libraries**:
  - Element Plus components
  - Tailwind CSS styling
  - Vite for builds

### Security
- API tokens via Passport
- CSRF protection for web routes
- Policies for model authorization

## Key Technologies
- **Backend**: Laravel, Redis, MySQL
- **Frontend**: Vue.js, Tailwind CSS, Vite
- **Real-time**: Laravel Echo, Pusher protocol