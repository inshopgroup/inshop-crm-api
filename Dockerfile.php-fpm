FROM inshopgroup/docker-inshop-crm-api-php-fpm-prod:latest

WORKDIR /var/www
ADD ./ /var/www

RUN cp .env.dist .env
RUN composer install

RUN chown -R www-data:www-data /var/www
RUN chmod +x /var/www/bin/entrypoint.sh

CMD /var/www/bin/entrypoint.sh
