# About
This app is available to import Netflix records from csv files (see https://www.kaggle.com/datasets/sayeeduddin/netflix-2025user-behavior-dataset-210k-records).
It also provides REST API to work with imported data.

## How to run import

- Clone the project
> git clone git@github.com:AlekseyKrupsky/laravel-netflix-import.git

- Copy env file and fill it with values
> cd laravel-netflix-import && cp .env.example .env

### Env file content
```
APP_NAME=Netflix dataset import
APP_ENV=local
# Will be generated automatically
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8081

DB_CONNECTION=mysql
DB_HOST=database
DB_PORT=3306

DB_DATABASE=netflix
DB_USERNAME=admin
# Add password for admin user
DB_PASSWORD=
# Add password for root user
DB_ROOT_PASSWORD=

# Override local app and database ports to avoid port conflicts
LOCAL_APP_PORT=
LOCAL_DB_PORT=
```

- Run application
> docker-compose build && docker-compose up

- Install composer dependencies and dump autoload
> docker exec netflix_phpfpm composer install
> docker exec netflix_phpfpm composer dump-autoload

- Run migrations
> docker exec netflix_phpfpm php artisan migrate:fresh

## Import data

Sample data files are in `import` directory. For convenience data file names hardcoded 
to `movies.csv`, `reviews.csv`, `users.csv`.

You should import movies and users first, because reviews have foreign fields from them.

Import commands support dry-run mode. In this case script only read files and make some validations
without saving date to database. Example:
> docker exec netflix_phpfpm php artisan app:movies-import --dry-run

Commands to import data to DB:

- Import movies
> docker exec netflix_phpfpm php artisan app:movies-import

- Import users
> docker exec netflix_phpfpm php artisan app:users-import

- Import reviews
> docker exec netflix_phpfpm php artisan app:reviews-import

## Api



