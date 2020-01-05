FROM ubuntu:latest

WORKDIR /var/www/html/uploader/project
#COPY --from=composer /code .

ENV DEBIAN_FRONTEND=noninteractive

RUN apt update -y
RUN apt install software-properties-common -y
RUN add-apt-repository ppa:ondrej/php

RUN apt-get update && apt-get install -yq --no-install-recommends \
    apt-utils \
    curl \
    git \
    apache2 \
    php7.4 \
    libapache2-mod-php7.4 \
    php7.4-cli \
    php7.4-json \
    php7.4-curl \
    php7.4-fpm \
    php7.4-gd \
    php7.4-ldap \
    php-mbstring \
    php7.4-mysql \
    php7.4-sqlite3 \
    php7.4-zip \
    php7.4-mbstring \
    php7.4-mysql \
    php7.4-zip \
    php7.4-intl \
    php7.4-xml \
    php-intl \
    php-imagick \
    openssh-client \
    openssh-server \
    graphicsmagick \
    ghostscript \
    mysql-client \
    iputils-ping \
    locales \
    cron\
    mc \
    wget \
    imagemagick \
    python3 \
    python3-pip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN pip3 install --upgrade setuptools
RUN pip3 install awscli --upgrade --user
COPY ./apache/cron /etc/cron.d/cron
RUN chmod 0644 /etc/cron.d/cron
RUN crontab /etc/cron.d/cron
RUN touch /var/log/cron.log
CMD cron && tail -f /var/log/cron.log

# Set locales
RUN locale-gen en_US.UTF-8 en_GB.UTF-8 de_DE.UTF-8 es_ES.UTF-8 fr_FR.UTF-8 it_IT.UTF-8 km_KH sv_SE.UTF-8 fi_FI.UTF-8

RUN a2enmod rewrite expires

# Add new non-root user to container
RUN groupadd -r uploader && useradd --no-log-init  -r -g uploader uploader

RUN mkdir /var/run/sshd
RUN echo 'root:screencast' |chpasswd
RUN sed -i 's/PermitRootLogin prohibit-password/PermitRootLogin yes/' /etc/ssh/sshd_config

EXPOSE 22
CMD ["/usr/sbin/sshd", "-D"]

# Copy apache2 config to run project under apache2 server
COPY ./apache/vhost-config.conf /etc/apache2/sites-available/vhost-config.conf
RUN a2dissite 000-default.conf
RUN a2ensite vhost-config.conf

RUN mkdir /var/www/html/uploader/storage
RUN chmod 775 /var/www/html/uploader/storage

# Create folder to put custom sh file that will be running when container will be created
# Copy the  sh file
# Set right permission to that file
RUN mkdir /uploader
COPY ./apache/run.sh /uploader/
RUN chmod 777 /uploader/run.sh

COPY ./apache/preset.sh /
RUN chmod 777 /preset.sh

# Run custom file sh to run apache2 and set right permissions for project
RUN find /var/www/html/uploader/project -type f -exec chmod 664 {} \;
#RUN find /var/www/html/uploader/project -type d -exec chmod 775 {} \;
RUN chown -R 1000:www-data /var/www/html/uploader/project
RUN chown -R uploader:www-data /var/www/html/uploader

# Expose port
EXPOSE 80

ENTRYPOINT ["/uploader/run.sh"]

