FROM rideron89/nginx-php:7.2.1

RUN apk add --no-cache \
    	$PHPIZE_DEPS \
    	openssl-dev \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb
