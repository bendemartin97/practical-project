package main

import (
    "flag"
    "github.com/gorilla/websocket"
    "log"
    "net/http"
)

var (
	addr = flag.String("addr", ":6002", "http service address")
)

var upgrader = websocket.Upgrader{}

func serveWs(w http.ResponseWriter, r *http.Request) {
	c, err := upgrader.Upgrade(w, r, nil)
	if err != nil {
		log.Print("upgrade:", err)
		return
	}
	defer c.Close()
	for {
		mt, message, err := c.ReadMessage()
		if err != nil {
			log.Println("read:", err)
			break
		}

		message = Calculate()
		err = c.WriteMessage(mt, message)
		if err != nil {
			log.Println("write:", err)
			break
		}
	}
}

func main() {
	flag.Parse()
	log.SetFlags(0)
	http.HandleFunc("/", serveWs)
	log.Fatal(http.ListenAndServe(*addr, nil))
}
