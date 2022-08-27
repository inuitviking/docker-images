# We want to use a minimal installation, because we don't need all the extra packages
FROM rockylinux:9-minimal
# Upgrade and install necessary minor packages
RUN microdnf upgrade --refresh -y; \
    microdnf update -y; \
    microdnf install openssl curl zip unzip epel-release -y
# Download, install, and enable repositories
RUN curl https://dl.fedoraproject.org/pub/epel/epel-release-latest-8.noarch.rpm --output epel-release-latest-8.noarch.rpm; \
    rpm -i epel-release-latest-8.noarch.rpm; \
    curl http://rpms.remirepo.net/enterprise/remi-release-9.rpm --output remi-release-9.rpm; \
    rpm -i remi-release-9.rpm; \
    microdnf module enable php:remi-8.1 -y
# Install PHP
RUN microdnf install php php-cli php-curl php-mysqlnd php-gd php-opcache php-zip php-intl php-common php-bcmath php-imap php-imagick php-xmlrpc php-json php-readline php-memcached php-redis php-mbstring php-apcu php-xml php-dom php-process -y; \
    php -v;
# Clean caches
RUN microdnf clean all
COPY docker-php-entrypoint /usr/local/bin/
ENTRYPOINT ["docker-php-entrypoint"]
# Start PHP CLI in interactive mode
CMD php -a
