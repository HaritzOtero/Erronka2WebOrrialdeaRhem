# Usamos la imagen oficial de PHP con Apache
FROM php:7.4-apache

# Instalamos la extensión mysqli
RUN docker-php-ext-install mysqli

# Copiamos los archivos del proyecto al contenedor
COPY ./erronka2_web/ /var/www/html/

# Exponemos el puerto 80
EXPOSE 80
