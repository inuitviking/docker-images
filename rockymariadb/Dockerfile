# We want to use a minimal installation, because we don't need all the extra packages
FROM rockylinux:9-minimal
COPY MariaDB.repo /etc/yum.repos.d/MariaDB.repo
# Install necessary software
RUN microdnf upgrade --refresh -y; \
    microdnf update -y; \
    microdnf install MariaDB-server MariaDB-client -y; \
    microdnf clean all
VOLUME /var/lib/mysql
EXPOSE 3306
CMD ["mariadbd"]