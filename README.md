
# Notificator

Application for saving your tasks. \
E-mail notifications about the upcoming date.


## Requirements
If you want to host this app yourself, you will need a server with:

- PHP 8.0 or newer
- HTTP server with PHP support (eg: Apache, Nginx)
- Composer
- Npm
- MySQL
## Installation

Installing the application in few steps 

1.
```bash
  git clone --branch main https://github.com/miominarik/notificator.git
  cd notificator
```
2. Setup Databse connection and SMTP server in .env file
3.
```bash
  composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev
  php artisan migrate
  php artisan cache:clear
  php artisan auth:clear-resets
  php artisan key:generate
  php artisan config:cache
  php artisan view:cache
  npm ci
  npm run production
  php artisan route:cache
```
4. Go to the Api table and add a new record that will contain the IP address of the server that will run CRON. \
Then just set CRON to run this address every hour \
GET https://Your-URL/api/main_check_agent
    
## License

Licensed under [the AGPL License](/LICENSE)

