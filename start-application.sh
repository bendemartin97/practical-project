#!/bin/zsh

root=$(pwd)

echo "INFO: Building FFI extension"
mkdir -p GO/ffi-tmp
cp -r GO/ffi/* GO/ffi-tmp
cp -r GO/go.*  GO/ffi-tmp
cp -r GO/shared/* GO/ffi-tmp
cd GO/ffi-tmp || exit

cwd=$(pwd)
# Build the docker image
docker run --rm -v "$PWD":"$cwd" -w "$cwd" -e GOOS=linux -e GOARCH=amd64 golang:1.20 go build -v --buildmode=c-shared -o ffi-extension.so

# Copy the shared object to the project root
cp ffi-extension.* $root/src
cd $root || exit
rm -rf GO/ffi-tmp

echo "INFO: FFI extension built successfully"
echo "INFO: Starting docker containers"
docker compose up -d --build
