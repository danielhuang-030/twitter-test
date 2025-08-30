# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Initial Setup

These commands are typically run once when setting up the project.

1.  **Copy environment file**: `cp .env.example .env` (and configure database/redis settings)
2.  **Generate app key**: `php artisan key:generate`
3.  **Create storage link**: `php artisan storage:link`
4.  **Install Horizon**: `php artisan horizon:install`
5.  **Setup Passport (for API authentication)**:
    - `php artisan passport:keys`
    - `php artisan passport:client --personal --no-interaction`

## Commands

### Backend (Laravel)

-   **Install dependencies**: `composer install`
-   **Run migrations**: `php artisan migrate`
-   **Run tests**: `vendor/bin/phpunit`
-   **Run a single test file**: `vendor/bin/phpunit tests/Unit/ExampleTest.php`
-   **Run queue workers (Horizon)**: `php artisan horizon`
-   **Linting**: `vendor/bin/pint`

### Frontend (Vue)

-   **Install dependencies**: `npm install`
-   **Run development server**: `npm run dev`
-   **Build for production**: `npm run build`

## Architecture

This is a Twitter clone project built with Laravel 12 and a Vue.js 3 frontend.

-   **Backend**: The core application logic resides in the `app/` directory, following the standard Laravel MVC structure.
    -   `app/Http/Controllers`: Contains the controllers that handle HTTP requests.
    -   `app/Models`: Defines the Eloquent models (e.g., User, Post).
    -   `app/Services`: Contains business logic decoupled from controllers.
    -   `routes/api.php` and `routes/web.php`: Define the API and web routes.
-   **Frontend**: The Vue.js source code is located in `resources/js/`.
-   **Database**: Migrations are in `database/migrations/`.
-   **Tests**: PHPUnit tests are in the `tests/` directory (`Feature` and `Unit`).

### Key Technologies & Packages

-   **Authentication**: `laravel/passport` is used for API authentication.
-   **Performance**: `laravel/octane` is used to serve the application with high-performance servers like Swoole.
-   **Queues**: `laravel/horizon` provides a dashboard and code-driven configuration for Redis queues.
-   **API Documentation**: API is documented using `zircote/swagger-php`.
-   **Real-time Events**: WebSockets (likely via Soketi) are used for instant notifications.
