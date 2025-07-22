# Demo Backend API

This Laravel application has been configured for **API-only** usage. It does not serve web pages or views - only JSON API responses.

## Configuration

This project has been optimized for API usage:

- ✅ API routes configured (`/api` prefix)
- ✅ CORS support enabled
- ✅ Session management disabled (stateless)
- ✅ API throttling enabled
- ✅ JSON-only responses
- ❌ Web views disabled
- ❌ Session middleware removed

## Available Endpoints

### Health Check
```bash
GET /api/health
```
Returns API health status and timestamp.

### API Status
```bash
GET /api/v1/status
```
Returns API version and status information.

### Root Endpoint
```bash
GET /
```
Returns information about API endpoints (JSON response).

## Quick Start

1. Install dependencies:
```bash
composer install
```

2. Generate application key (if not already set):
```bash
php artisan key:generate
```

3. Run migrations:
```bash
php artisan migrate
```

4. Start the development server:
```bash
php artisan serve
```

## Testing API Endpoints

Test the API using curl:

```bash
# Health check
curl http://localhost:8000/api/health

# API status
curl http://localhost:8000/api/v1/status

# Root endpoint
curl http://localhost:8000/
```

## API Development

Add your API routes in `routes/api.php`. All routes will automatically have the `/api` prefix and appropriate middleware applied.

Example:
```php
Route::middleware(['api'])->group(function () {
    Route::prefix('v1')->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        // Add more routes here...
    });
});
```

## Features

- Laravel 12.x
- PHP 8.2+
- SQLite database (default)
- API-only configuration
- CORS enabled
- Request throttling
- JSON error responses
- Health check endpoint

## Environment

This application runs in API-only mode. Session handling is disabled by default since APIs should be stateless.

For authentication, consider using:
- Laravel Sanctum for token-based auth
- Laravel Passport for OAuth2
- JWT tokens
- API keys

## Notes

This application is configured to serve only API endpoints. If you need to add web functionality, you'll need to:

1. Re-enable web middleware
2. Add blade view support
3. Configure session management
4. Update routing configuration

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
