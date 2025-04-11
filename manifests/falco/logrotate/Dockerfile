FROM alpine:latest

RUN apk add --no-cache logrotate

CMD ["sh", "-c", "while true; do logrotate -v /etc/logrotate.d/falco || echo 'logrotate failed'; sleep 3600; done"]

