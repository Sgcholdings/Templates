# !important

- make sure you have laravel installed in your machine
- copy and paste the project folder(ems) to you xampp or mamp htdocs
- create a .env file in app directory
- copy && paste the .env.example
- add this REDIRECT = "http://localhost:4200/login" at the end of .env
- make sure in your database you have the db created eg(larangular db)

# Run

- composer update

- // Don't forget to run below command which will generate an application (encryption) key and add it to the env file.

- php artisan key:generate 

# Send an email

- also remove MAIL_FROM_ADRRESS

- also remove FROM_NAME
