##
## Video Service Docker image build script
##

## 1. Builder. Collect build time dependencies and prepare the code

FROM klinktech/k-box-ci-pipeline-php:8.1 AS builder

LABEL oot.builder="video-builder"

COPY --chown=php:php . /var/www/html
RUN \
    mkdir -p "storage/framework/cache" &&\
    mkdir -p "storage/framework/cache/data" &&\
    mkdir -p "storage/framework/sessions" &&\
    mkdir -p "storage/framework/views" &&\
    mkdir -p "storage/logs" &&\
    composer install --no-dev --prefer-dist
    
RUN \
    yarn config set cache-folder .yarn && \
    yarn install --link-duplicates && \
    yarn run production

## 2. Packaging. Generate the production Docker image

FROM php:8.1.3-fpm AS php

LABEL maintainer="OneOffTech <info@oneofftech.xyz>" \
  org.label-schema.name="oneofftech/video-service" \
  org.label-schema.description="Docker image for the OneOffTech Video Service. Web application for self-hosting public videos." \
  org.label-schema.schema-version="1.0" \
  org.label-schema.vcs-url="https://github.com/OneOffTech/video-service/"

## Default environment variables
ENV PHP_MAX_EXECUTION_TIME 120
ENV PHP_MAX_INPUT_TIME 120
ENV PHP_MEMORY_LIMIT 2048M
ENV APP_CODE_DIR /var/www/html

## Install libraries, envsubst, supervisor and php modules
RUN apt-get update -yqq && \
    apt-get install -yqq --no-install-recommends \ 
        locales \
        supervisor \
        cron \
    && curl -sSLf \
        -o /usr/local/bin/install-php-extensions \
        https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions && \
    chmod +x /usr/local/bin/install-php-extensions && \
    IPE_GD_WITHOUTAVIF=1 install-php-extensions \
        bcmath \
        bz2 \
        exif \
        gd \
        intl \
        pdo_mysql \
        pcntl \
        opcache \
        zip \
    && docker-php-source delete \
    && apt-get autoremove -yq --purge \
    && apt-get autoclean -yq \
    && apt-get clean \
    && rm -rf /var/cache/apt/ /var/lib/apt/lists/* /var/log/* /tmp/* /var/tmp/* /usr/share/doc /usr/share/doc-base /usr/share/groff/* /usr/share/info/* /usr/share/linda/* /usr/share/lintian/overrides/* /usr/share/locale/* /usr/share/man/* /usr/share/locale/* /usr/share/gnome/help/*/* /usr/share/doc/kde/HTML/*/* /usr/share/omf/*/*-*.emf



## Forces the locale to UTF-8
RUN locale-gen "en_US.UTF-8" \
    && DEBIAN_FRONTEND=noninteractive dpkg-reconfigure locales \
 	&& locale-gen "C.UTF-8" \
 	&& DEBIAN_FRONTEND=noninteractive dpkg-reconfigure locales \
 	&& /usr/sbin/update-locale LANG="C.UTF-8"

## FFmpeg installation
## Install the current release as available on https://johnvansickle.com/ffmpeg/
ENV FFMPEG_BINARIES /usr/local/bin/ffmpeg
ENV FFPROBE_BINARIES /usr/local/bin/ffprobe

RUN set -e; \
    linux_url='https://johnvansickle.com/ffmpeg/releases/ffmpeg-release-amd64-static.tar.xz'; \
    linux_temp_archive='/tmp/ffmpeg-release-amd64-static.tar.xz'; \
    linux_name='ffmpeg-linux-x64'; \
    linux_temp="/usr/local/bin/"; \
    cd /tmp/ \
    && echo "Downloading FFmpeg release to $linux_temp_archive" \
    && curl -sSLf https://johnvansickle.com/ffmpeg/releases/ffmpeg-release-amd64-static.tar.xz -o $linux_temp_archive \
    && echo "Downloading FFmpeg checksum to ${linux_temp_archive}.md5" \
    && curl -sSLf https://johnvansickle.com/ffmpeg/releases/ffmpeg-release-amd64-static.tar.xz.md5 -o "${linux_temp_archive}.md5" \
    && md5sum -c "${linux_temp_archive}.md5" \
    && tar -xf $linux_temp_archive --wildcards -O '**/ffmpeg' > "$linux_temp/ffmpeg" \
    && tar -xf $linux_temp_archive --wildcards -O '**/ffprobe' > "$linux_temp/ffprobe" \
    && tar -xf $linux_temp_archive --wildcards -O '**/GPLv3.txt' > "$linux_temp/FFMPEG-LICENSE" \
    && tar -xf $linux_temp_archive --wildcards -O '**/readme.txt' > "$linux_temp/FFMPEG-README.txt" \
    && rm -rf /tmp/*

## NGINX installation
### The installation procedure is heavily inspired from https://github.com/nginxinc/docker-nginx
## TODO: Would probably be better to include NGINX installation within PHP extensions and dependencies
RUN set -e; \
	NGINX_GPGKEY=573BFD6B3D8FBC641079A6ABABF5BD827BD9BF62; \
	NGINX_VERSION=1.20.2-1~bullseye; \
	found=''; \
	apt-get update; \
	apt-get install --no-install-recommends --no-install-suggests -y gnupg1 ca-certificates; \
	for server in \
		hkp://keyserver.ubuntu.com:80 \
		pgp.mit.edu \
	; do \
		echo "Fetching GPG key $NGINX_GPGKEY from $server"; \
		apt-key adv --keyserver "$server" --keyserver-options timeout=10 --recv-keys "$NGINX_GPGKEY" && found=yes && break; \
	done; \
	test -z "$found" && echo >&2 "error: failed to fetch GPG key $NGINX_GPGKEY" && exit 1; \
    echo "deb https://nginx.org/packages/debian/ bullseye nginx" >> /etc/apt/sources.list.d/nginx.list \
	&& apt-get update \
	&& apt-get install --no-install-recommends --no-install-suggests -y \
						ca-certificates \
						nginx=${NGINX_VERSION} \
    && apt-get remove --purge --auto-remove -y gnupg1  \
    && apt-get remove --purge --auto-remove -y \
    && rm -rf /etc/apt/sources.list.d/nginx.list

## Configure cron to run Laravel scheduler
RUN echo '* * * * * php /var/www/html/artisan schedule:run >> /dev/null 2>&1' | crontab -

## Copy NGINX default configuration
COPY docker/nginx-default.conf /etc/nginx/conf.d/default.conf

## Copy additional PHP configuration files
COPY docker/php/php-*.ini /usr/local/etc/php/conf.d/

## Override the php-fpm additional configuration added by the base php-fpm image
COPY docker/php/zz-docker.conf /usr/local/etc/php-fpm.d/

## Copy supervisor configuration
COPY docker/supervisor/services.conf /etc/supervisor/conf.d/

## Copying custom startup scripts
COPY docker/configure.sh /usr/local/bin/configure.sh
COPY docker/start.sh /usr/local/bin/start.sh
COPY docker/db-connect-test.php /usr/local/bin/db-connect-test.php

RUN chmod +x /usr/local/bin/configure.sh && \
    chmod +x /usr/local/bin/start.sh

## Copy the application code
COPY \
    --chown=www-data:www-data \
    . /var/www/html/

## Copy in the dependencies from the previous buildstep
COPY \
    --from=builder \
    --chown=www-data:www-data \
    /var/www/html/vendor/ \
    /var/www/html/vendor/

COPY \
    --from=builder \
    --chown=www-data:www-data \
    /var/www/html/public/ \
    /var/www/html/public/

ENV STORAGE_PATH "/var/www/html/storage"

WORKDIR /var/www/html

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/start.sh"]

ARG BUILD_DATE
ARG BUILD_VERSION
ARG BUILD_COMMIT

LABEL version=$BUILD_VERSION \
  org.label-schema.build-date=$BUILD_DATE \
  org.label-schema.vcs-ref=$BUILD_COMMIT

