FROM alpine:latest

RUN apk add --no-cache logrotate
RUN mkdir -p /var/lib/logrotate

ENTRYPOINT ["logrotate"]
CMD ["-s", "/var/lib/logrotate/logrotate.status", "/etc/logrotate.conf"]

