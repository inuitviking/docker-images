# We want to use a minimal installation, because we don't need all the extra packages
FROM rockylinux:9-minimal
# Install necessary software
RUN microdnf upgrade --refresh -y; \
    microdnf update -y; \
    microdnf install openssl httpd mod_ssl -y; \
    microdnf clean all
# Below fixes a default bug when installing mod_ssl: https://community.letsencrypt.org/t/localhost-crt-does-not-exist-or-is-empty/103979/2
RUN /usr/libexec/httpd-ssl-gencerts; \
    httpd -t; \
    httpd -v
# Set working directory
WORKDIR /var/www
# Start HTTPD
CMD /usr/sbin/httpd -DFOREGROUND
