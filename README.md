# Logistics Platform API

## Introduction
This Laravel backend API provides a comprehensive solution for shipment management with:
- User authentication and role-based access
- Shipment creation with automatic geolocation
- Real-time shipment tracking
- System monitoring and audit logs
- Modular architecture following SOLID principles

## Prerequisites
- Git installed
- For Docker: Docker Desktop
- For Non-Docker:
    - PHP ≥ 8.1
    - Composer 2.6+
    - MySQL 5.7+

## Docker Setup (Laravel Sail)

### 1. Clone Repository
```bash
git clone [https://github.com/TunjiTofu/logistics-platform.git](https://github.com/TunjiTofu/logistics-app.git)
cd logistics-platform
composer install
```

### 2. Install dependencies using Sail's PHP 8.3 image
```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v $(pwd):/var/www/html \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs
```

### 3. Start Container
```bash
./vendor/bin/sail up -d
```

### 4. Environment Setup
```bash
cp .env.example .env
./vendor/bin/sail artisan key:generate
```

### 5. Database Setup
```bash
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

```

### 6. Run Migrations and Seed Database
```bash
./vendor/bin/sail artisan migrate --seed
```

### 7. Add your OpenCage API key to .env
```bash
OPENCAGE_API_KEY=your_api_key_here
```

### 8. Queue Processing For background job processing:
```bash
./vendor/bin/sail artisan horizon
```

### 9. Access the Application
[(http://localhost)](http://localhost)

### 10. Run Unit Tests
```bash
./vendor/bin/sail test
```

## Running the endpoints
### Check the API documention for the endpoints


## Troubleshooting

| Issue                             | Solution                          |
|-----------------------------------|-----------------------------------|
| PHP 8.3 not found                 | Verify installation with `php -v` |
| Composer errors                   | Run `composer self-update`        |
| Sail containers not starting      | Ensure Docker Desktop is running  |
| Database issues                   | Verify credentials in `.env`      |
| Authentication errors             | Run passport:install              |
| Geolocation failures              | Confirm API key is valid          |
| Queue jobs not processing         | Start queue worker                |

## Architecture Highlights:
 - Modular Design: Services, Repositories, DTOs ✅
 - SOLID Principles: Applied across codebase ✅
 - Security: Sanctum authentication, role-based access ✅
 - Geolocation: OpenCage integration for address coordinates ✅
 - Logging: Comprehensive action tracking ✅

## Further Improvements:
 - Implement real-time tracking updates 
 - Add email/SMS notifications 
 - Integrate with mobile applications

## API Documentation
[Click this Link Access the Complete API Documentation]([https://documenter.getpostman.com/view/17648045/2sB2xCh9Lx](https://documenter.getpostman.com/view/17648045/2sB2xCh9Lx))

