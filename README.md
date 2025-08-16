# Larataskr Setup Instructions

## Requirements
- PHP 8.2+
- Composer
- Node.js 18+ and npm
- MySQL 8+ or compatible

## Installation
1. Clone the repository and enter the project folder:
    git clone https://github.com/DemonHulk/larataskr
    cd larataskr

2. Install PHP dependencies
    composer install
    php artisan key:generate

3. Edit .env to set up the MySQL database connection:
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=larataskr
    DB_USERNAME=root
    DB_PASSWORD=mypassword

4. Create the database in MySQL:
    CREATE DATABASE larataskr;

5. Run database migrations:
    php artisan migrate

6. Install Node dependencies and build frontend assets:
    npm install
    npm run dev

7. Start the Laravel development server:
    php artisan serve

8. Open the application in your browser:
    http://127.0.0.1:8000
     