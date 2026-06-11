FROM php:8.2-apache
# Instala dependências necessárias para o Composer
RUN apt-get update && apt-get install -y git unzip
# Instala o PDO do MySQL
RUN docker-php-ext-install pdo pdo_mysql
# Copia o Composer oficial para dentro do container
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer