# Image page: <https://hub.docker.com/_/golang>
FROM golang:1.20-alpine as builder

WORKDIR /src
# Copy server sources into image
COPY ./GO/go.mod ./GO/go.sum ./GO/message-queue/main.go ./
COPY ./GO/shared/* ./
# Build goride server
RUN go mod download

RUN go mod verify

RUN env CGO_ENABLED=0 GOOS=linux GOARCH=amd64 go build -v -ldflags="-s" -o ./server ./*.go
RUN chmod +x ./server
# Image page: <https://hub.docker.com/_/alpine>
FROM alpine:3.15 as runtime

# Unprivileged user creation <https://stackoverflow.com/a/55757473/12429735RUN>
RUN adduser \
    --disabled-password \
    --gecos "" \
    --home "/nonexistent" \
    --shell "/sbin/nologin" \
    --no-create-home \
    --uid "10001" \
    "appuser"

# Use an unprivileged user
USER appuser:appuser

# Import from builder
COPY --from=builder /src/server /usr/bin/server

EXPOSE 6002

ENTRYPOINT ["/usr/bin/server"]
