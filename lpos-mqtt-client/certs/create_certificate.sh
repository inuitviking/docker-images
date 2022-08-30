#!/bin/bash

IP=$(hostname -I | awk '{print $1}')

mkdir -p "${IP}"

openssl genrsa -out "${IP}"/server.key 4096
openssl req -new -key "${IP}"/server.key -out "${IP}"/server-cert-request.csr -sha512 -subj "/C=DK/ST=Syddanmark/L=Aabenraa/O=MAC/OU=IT Department/CN=${IP}"
openssl x509 -req -in "${IP}"/server-cert-request.csr -CA 192.168.95.115/ca-root-cert.crt -CAkey 192.168.95.115/ca.key -CAcreateserial -out "${IP}"/server.crt -days 3653 -subj "/C=DK/ST=Syddanmark/L=Aabenraa/O=MAC/OU=IT Department/CN=${IP}"
