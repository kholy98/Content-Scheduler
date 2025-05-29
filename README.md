

## Installation

## 1. Clone the Repository

```bash
git clone https://github.com/kholy98/Content-Scheduler.git
cd Content-Scheduler
```

## 2. Install PHP Dependencies

Install Composer dependencies:
```bash
composer install
```


## 3. Set Up the Environment File

Copy the .env.example file to .env:

```bash
cp .env.example .env
```

Update the .env file with your database credentials:
```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=content_db
DB_USERNAME=root
DB_PASSWORD=
```


## 4. Generate Application Key

```bash
php artisan key:generate
```


## 5. Run Migrations With Seeder

```bash
php artisan migrate --seed
```


## 6. Install Frontend Dependencies

```bash
cd frontend
npm install
```


## 7. Compile Assets

```bash
npm run dev
```


## 8. Start the Development Server

```bash
php artisan serve
```

