# We want to use a minimal installation, because we don't need all the extra packages
FROM rockylinux:9-minimal
# Create the necessary users
RUN groupadd -r mysql && useradd -r -g mysql mysql

######################################################################################################################################
# Install gosu.  https://github.com/tianon/gosu                                                                                      #
# Thanks to this comment: https://gist.github.com/rafaeltuelho/6b29827a9337f06160a9?permalink_comment_id=2741406#gistcomment-2741406 #
# And also this page: https://github.com/tianon/gosu/blob/master/INSTALL.md                                                          #
# As well as this section: https://github.com/tianon/gosu/blob/master/README.md#installation                                         #
######################################################################################################################################
# Define the gosu version to install
ENV GOSU_VERSION=1.14
# Check the keys for gosy
RUN gpg --batch --keyserver hkps://keys.openpgp.org --recv-keys B42F6819007F00F88E364FD4036A9C25BF357DD4
# download gosu
RUN curl -o /usr/local/bin/gosu -SL "https://github.com/tianon/gosu/releases/download/${GOSU_VERSION}/gosu-arm64" \
    && curl -o /usr/local/bin/gosu.asc -SL "https://github.com/tianon/gosu/releases/download/${GOSU_VERSION}/gosu-arm64.asc"
# Verify gosu and make gosu executable
RUN gpg --batch --verify /usr/local/bin/gosu.asc /usr/local/bin/gosu \
    && chmod +x /usr/local/bin/gosu
# Verify that the gosu works
RUN  gosu --version \
    && gosu nobody true

# Install mariadb repository and install the software
COPY MariaDB.repo /etc/yum.repos.d/MariaDB.repo
# Install necessary software
RUN microdnf upgrade --refresh -y; \
    microdnf update -y; \
    microdnf install openssl curl zip unzip epel-release -y; \
    microdnf install jemalloc jemalloc-devel pwgen tzdata xz xz-libs zstd -y; \
    microdnf install MariaDB-server MariaDB-client -y; \
    microdnf clean all
COPY my.cnf /etc/my.cnf
# Make an entrypoint directory; see https://github.com/MariaDB/mariadb-docker/blob/97e6715fb9f86010de510eef718f7341e3011c25/10.6/Dockerfile#L39
RUN mkdir /docker-entrypoint-initdb.d
# Make sure the necessary scripts are installed, see https://github.com/MariaDB/mariadb-docker/blob/97e6715fb9f86010de510eef718f7341e3011c25/10.6/Dockerfile#L127
COPY healthcheck.sh /usr/local/bin/healthcheck.sh
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
# Make the entrypoint this script
ENTRYPOINT [ "docker-entrypoint.sh" ]
RUN mkdir -p /var/lib/mysql /var/run/mysqld; \
    chown -R mysql:mysql /var/lib/mysql /var/run/mysqld; \
    # ensure that /var/run/mysqld (used for socket and lock files) is writable regardless of the UID our mysqld instance ends up having at runtime
    chmod 777 /var/run/mysqld; 
# Define the database volume
VOLUME /var/lib/mysql
# Expose the standard port
EXPOSE 3306
# Start mariadbd
CMD ["mariadbd"]
