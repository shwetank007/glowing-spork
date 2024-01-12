### Steps for running the project

Step 1 - Clone the repository.

Step 2 - Run composer install.

Step 3 - Run cp .env.example .env.

Step 4 - Create a database on your local.

Step 5 - Put the credentials over .env file.

Step 4 - Run php artisan key:generate.

Step 5 - Run php artisan migrate.

Step 6 - Run php artisan db:seed --class=UserSeeder.

Step 7 - Run php artisan serve.

Step 8 - Go to link localhost:8000.