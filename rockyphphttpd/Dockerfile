# We want to use a minimal installation, because we don't need all the extra packages
FROM rockylinux:9-minimal
# Install necessary software
RUN microdnf upgrade --refresh -y; \
    microdnf update -y; \
    microdnf install openssl curl zip unzip epel-release httpd mod_ssl -y
# Download, install, and enable repositories
RUN curl https://dl.fedoraproject.org/pub/epel/epel-release-latest-8.noarch.rpm --output epel-release-latest-8.noarch.rpm; \
    rpm -i epel-release-latest-8.noarch.rpm; \
    curl http://rpms.remirepo.net/enterprise/remi-release-9.rpm --output remi-release-9.rpm; \
    rpm -i remi-release-9.rpm; \
    microdnf module enable php:remi-8.0 -y
# Install PHP
RUN microdnf install php php-fpm php-cli php-curl php-mysqlnd php-gd php-opcache php-zip php-intl php-common php-bcmath php-imap php-imagick php-xmlrpc php-json php-readline php-memcached php-redis php-mbstring php-apcu php-xml php-dom php-process -y; \
    php -v;
RUN microdnf clean all
COPY configs/www.conf /etc/php-fpm.d/www.conf
RUN mkdir -p /run/php-fpm
# Below fixes a default bug when installing mod_ssl: https://community.letsencrypt.org/t/localhost-crt-does-not-exist-or-is-empty/103979/2
RUN /usr/libexec/httpd-ssl-gencerts; \
    httpd -t; \
    httpd -v
# Set working directory
WORKDIR /var/www
# Start HTTPD
CMD php-fpm; /usr/sbin/httpd -DFOREGROUND
