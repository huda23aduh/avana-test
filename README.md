# avana-test

CONTENTS OF THIS FILE
---------------------

 * Introduction
 * Requirements
 * Installation

 INTRODUCTION
------------
This project is part of avana's recruitment process

 REQUIREMENTS
------------
- laravel ^8.0
- php ^8.0

 INSTALLATIONS
------------

1. Create / install fresh laravel project on your local machine. For details, visit : https://laravel.com/docs/9.x/installation
2. After laravel successfully created / installed, open cmd / terminal then navigate to laravel project root.
3. Configure your DB configuration in .env file
4. Add avanahuda/avanatest package via composer. Type :
    --> composer require avanahuda/avanatest @dev
    --> composer dump-autoload     
5. Do migration. type this command on your cmd / terminal
    --> php artisan migrate
6. Run laravel project using this command :
    --> php artisan serve

This project use laravel default port (8000)
#### End ###

