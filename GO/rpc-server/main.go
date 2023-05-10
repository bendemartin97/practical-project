package main

import (
    "fmt"
    goridgeRpc "github.com/roadrunner-server/goridge/v3/pkg/rpc"
    "net"
    "net/rpc"
    "os"
)

type App struct{}

func (s *App) Calculate(_, r *string) error {

	result := Calculate()
	*r = fmt.Sprintf(string(result))
	return nil
}

func main() {
	var ln net.Listener
	var err error
	if len(os.Args) == 2 {
		ln, err = net.Listen("unix", os.Args[1])
	} else {
		ln, err = net.Listen("tcp", ":6001")
	}

	if err != nil {
		panic(err)
	}

	err = rpc.Register(new(App))
	if err != nil {
		panic(err)
	}
	fmt.Println("Server has been started")
	for {
		conn, err2 := ln.Accept()
		if err2 != nil {
			continue
		}
		go rpc.ServeCodec(goridgeRpc.NewCodec(conn))
	}
}
