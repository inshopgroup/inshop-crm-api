FROM inshopgroup/crm-api-docker-nginx-prod:latest

WORKDIR /var/www

ADD ./public /var/www/public

# permissions
RUN chown -R www-data:www-data /var/www/public
