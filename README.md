
Building Docker Doodles
-----------------------

Build the Containers
> docker-compose up -d --build --force-recreate

### List of  Containers
app_container 

nginx_container

# Access to container
> docker exec -it app_container bash

# Install PHP Dependencies
> composer install



# Access to App

http://localhost:9090


## helper utility

http://localhost:9090/import
