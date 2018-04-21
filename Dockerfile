FROM buonzz/php-production-cli:latest

ENV UNIFI_URL https://unifi.hostname
ENV UNIFI_USER unifi_alerts
ENV UNIFI_PASS unifi_alerts
ENV SITE_ID default
ENV CONTROLLER_VERSION 5.6.36

ENV SMTP_HOST localhost
ENV SMTP_PORT 25
#ENV SMTP_USER
#ENV SMTP_PASS
ENV SMTP_FROM unifi_alerts@unifi.hostname
ENV SMTP_TO you@somewhere.com

ADD composer.json /var/www
RUN composer install

ADD . /var/www

RUN mkdir /var/www/data
VOLUME /var/www/data

ENTRYPOINT exec php unifi-alerts-daemon.php
