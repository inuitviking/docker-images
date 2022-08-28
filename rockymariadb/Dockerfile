# We want to use a minimal installation, because we don't need all the extra packages
FROM rockylinux:9-minimal
RUN groupadd -r mysql && useradd -r -g mysql mysql
COPY MariaDB.repo /etc/yum.repos.d/MariaDB.repo
# Install necessary software
RUN microdnf upgrade --refresh -y; \
    microdnf update -y; \
    microdnf install MariaDB-server MariaDB-client -y; \
    microdnf clean all
RUN mkdir /docker-entrypoint-initdb.d
COPY healthcheck.sh /usr/local/bin/healthcheck.sh
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
ENTRYPOINT [ "docker-entrypoint.sh" ]
VOLUME /var/lib/mysql
EXPOSE 3306
CMD su mysql -c mariadbd