# Usar imagen oficial de PHP con Apache
FROM php:8.1-apache

# Instalar extensiones PHP necesarias
RUN apt-get update && apt-get install -y \
    default-mysql-client \
    && docker-php-ext-install mysqli \
    && docker-php-ext-enable mysqli \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Habilitar módulo rewrite de Apache
RUN a2enmod rewrite

# Cambiar el DocumentRoot a /var/www/html
WORKDIR /var/www/html

# Copiar archivos de la aplicación
COPY . .

# Dar permisos a carpetas que necesitan escritura
RUN chmod -R 755 /var/www/html && \
    chmod -R 777 /var/www/html/uploads && \
    chmod -R 777 /var/www/html/img

# Exponer puerto
EXPOSE 80

# El comando por defecto del contenedor es Apache
CMD ["apache2-foreground"]
