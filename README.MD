# About
This app is available to import Netflix records from csv files (see https://www.kaggle.com/datasets/sayeeduddin/netflix-2025user-behavior-dataset-210k-records).
It also provides REST API to work with imported data.

## How to run import

- Clone the project
``` 
git clone git@github.com:AlekseyKrupsky/laravel-netflix-import.git
```

- Copy env file and fill it with values
> cd laravel-netflix-import && cp .env.example .env

### Env file content
```
APP_NAME=Netflix dataset import
APP_ENV=local
# Add secure string here
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

## REST API documentation 

### Users

- List of users. Support pagination and filtration (`id`, `email`, `first_name`, `last_name`, `age`, `gender`).
- Field `id` support multiple value
> http://localhost:8080/api/users
> http://localhost:8080/api/users?page=2
> http://localhost:8080/api/users?email=adkinsbrian@example.net
> http://localhost:8080/api/users?id=1,2,3,4

- User details. Include related movies and reviews.
> http://localhost:8080/api/users/1

### Movies

- List of movies. Support pagination and filtration (`id`, `title`, `genre`, `genre_secondary`, `year`).
- Field `id` support multiple value
> http://localhost:8080/api/movies
> http://localhost:8080/api/movies?page=2
> http://localhost:8080/api/movies?genre=History&year=2009
> http://localhost:8080/api/movies?id=1,2,3,4

- Movie details. Include related users and reviews.
> http://localhost:8080/api/movies/1

### Reviews

- List of reviews. Support pagination and filtration (`id`, `user`, `movie`, `review_date`, `device_type`, `is_verified_watch`).
- Field `id` support multiple value
> http://localhost:8080/api/reviews
> http://localhost:8080/api/reviews?page=2
> http://localhost:8080/api/reviews?user=1
> http://localhost:8080/api/reviews?id=1,2,3,4
> http://localhost:8080/api/reviews?is_verified_watch=false
> http://localhost:8080/api/reviews?is_verified_watch=true

- Review details. Include related movie and user.
> http://localhost:8080/api/reviews/1

- Add new review. Return response with status code 201.
```
curl --location --request POST 'localhost:8081/api/reviews' \
--header 'Accept: application/json' \
--header 'Content-Type: application/json' \
--data-raw '{
    "user_id": 123,
    "movie_id": 321,
    "rating": 4,
    "review_date": "2021-11-23",
    "device_type": "Mobile",
    "is_verified_watch": true,
    "helpful_votes": 7,
    "total_votes": 6,
    "review_text": "Reviews",
    "sentiment": "positive",
    "sentiment_score": "0.678"
}'
```

- Update review with given `id`. Can be updated any number of fields. On success return status code 200.

```
curl --location --request PATCH 'localhost:8081/api/reviews/1' \
--header 'Accept: application/json' \
--header 'Content-Type: application/json' \
--data-raw '{
    "rating": 3,
    "review_date": "2021-11-23",
    "device_type": "Laptop"
}'
```

- Delete review by `id`. On success return status code 204 without content.

```
curl --location --request DELETE 'localhost:8081/api/reviews/1'
```
