// in this file i will apply migrations,seeds and storage link in docker container
#!/bin/bash
docker exec -it laravel_app php artisan migrate:fresh --force
docker exec -it laravel_app php artisan db:seed --force
docker exec -it laravel_app php artisan storage:link --force