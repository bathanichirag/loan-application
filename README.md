# loan-application
___________________________________
Laravel 9.x
PHP 8+ required 
____________________________________
Take clone using this URL: https://github.com/bathanichirag/loan-application.git
Then run: composer install

Create .env file from .env.example

setup DB credentials in .env file.

__________________________________
Run below Commands For setup

php artisan migrate

php artisan db:seed

php artisan passport:install

php artisan optimize:clear

php artisan serve

___________________________________

Admin credentials

Username: admin@admin.com

Password: Admin@123
___________________________________

API LIST

Admin / Customer Login: http://127.0.0.1:8000/api/login

Register Customer: http://127.0.0.1:8000/api/register

Logout Admin / Customer: http://127.0.0.1:8000/api/logout

Apply Loan: http://127.0.0.1:8000/api/apply-loan

Admin Change Loan Status: http://127.0.0.1:8000/api/change-loan-status

Loan Payments: http://127.0.0.1:8000/api/loan-payment

Get Loan List: http://127.0.0.1:8000/api/get-loans?limit=10&offset=0

Get Loan Payments: http://127.0.0.1:8000/api/get-loan-payments?limit=10&offset=0&loan_id=1

______________________________________

FOR TESTING

setup DB credentials in .env.testing file.

Run below Commands

php artisan migrate --env=testing

php artisan test

php artisan test --coverage

______________________________________
