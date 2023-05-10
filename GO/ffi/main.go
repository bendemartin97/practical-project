package main

import (
	"C"
)

//export calculate
func calculate() *C.char {
	return (C.CString)(string(Calculate()))
}

func main() {}
