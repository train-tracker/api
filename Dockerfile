#
# Based on RethinkDB Dockerfile
#
# https://github.com/dockerfile/rethinkdb
#

# Pull base image.
FROM dockerfile/ubuntu

# Install PHP, Apache
RUN \
  apt-get update && \
  apt-get install -y apache2 && \
  apt-get install -y php5 libapache2-mod-php5 && \
  apt-get install -y php5-xdebug && \
  apt-get install -y php5-curl && \
  /etc/init.d/apache2 restart &&\
  echo '# Added for xdebug' >> /etc/php5/apache2/php.ini && \
  echo 'zend_extension="/usr/lib/php5/20100525/xdebug.so"' >> /etc/php5/apache2/php.ini && \
  echo 'xdebug.remote_enable=1' >> /etc/php5/apache2/php.ini && \
  echo 'xdebug.remote_handler=dbgp xdebug.remote_mode=req' >> /etc/php5/apache2/php.ini && \
  echo 'xdebug.remote_host=127.0.0.1 xdebug.remote_port=9000' >> /etc/php5/apache2/php.ini && \
  a2enmod rewrite

# Install composer
RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/bin/composer

# Install RethinkDB.
RUN \
  echo "deb http://download.rethinkdb.com/apt `lsb_release -cs` main" > /etc/apt/sources.list.d/rethinkdb.list && \
  wget -O- http://download.rethinkdb.com/apt/pubkey.gpg | apt-key add - && \
  apt-get update && \
  apt-get install -y rethinkdb && \
  rm -rf /var/lib/apt/lists/*

# Add the source code
RUN rm -rf /var/www
ADD . /var/www
RUN chown -R www-data:www-data /var/www
ENV APACHE_LOG_DIR /var/log/apache2
ADD apache.conf /etc/apache2/sites-available/000-default.conf
RUN cd /var/www/ && /usr/bin/composer install

# Define mountable directories.
VOLUME ["/data"]

# Define working directory.
WORKDIR /data

# Define default command.
CMD service apache2 start && rethinkdb --bind all

# Expose ports.
#   - 80: Apache
#   - 8080: web UI
#   - 28015: process
#   - 29015: cluster
EXPOSE 80
EXPOSE 8080
EXPOSE 28015
EXPOSE 29015