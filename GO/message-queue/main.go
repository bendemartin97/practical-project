package main

import (
	"context"
	"fmt"

	"log"
	"time"

	amqp "github.com/rabbitmq/amqp091-go"
)

func failOnError(err error, msg string) {
	if err != nil {
		log.Panicf("%s: %s", msg, err)
	}
}

var (
	connection   *amqp.Connection
	goToPhpQueue amqp.Queue
	phpToGoQueue amqp.Queue
)

const GO_TO_PHP_QUEUE_NAME = "go-to-php"
const PHP_TO_GO_QUEUE_NAME = "php-to-go"

func createQueue(queue amqp.Queue, name string, channel *amqp.Channel) {
	fmt.Println("Declaring queue with " + name)
	queue, err := channel.QueueDeclare(
		name,  // name
		false, // durable
		false, // auto delete
		false, // exclusive
		false, // no wait
		nil,   // args
	)
	failOnError(err, "Failed to declare a queue")
}

func main() {
	for {
		var err error
		connection, err = amqp.Dial("amqp://guest:guest@rabbitmq3:5672/")
		if err == nil {
			break
		}
		defer connection.Close()
	}

	fmt.Println("Successfully connected to RabbitMQ instance")
	channel, err := connection.Channel()
	if err != nil {
		failOnError(err, "Can not connect to channel")
	}
	defer channel.Close()

	createQueue(goToPhpQueue, GO_TO_PHP_QUEUE_NAME, channel)
	createQueue(phpToGoQueue, PHP_TO_GO_QUEUE_NAME, channel)

	msgs, err := channel.Consume(
		PHP_TO_GO_QUEUE_NAME, // queue
		"",                   // consumer
		true,                 // auto ack
		false,                // exclusive
		false,                // no local
		false,                // no wait
		nil,                  //args
	)
	if err != nil {
		panic(err)
	}

	// print consumed messages from queue
	forever := make(chan bool)
	go func() {
		for msg := range msgs {
			fmt.Printf("Received Message: %s\n", msg.Body)
			send()
		}
	}()

	fmt.Println("Waiting for messages...")
	<-forever
}

func send() {
	conn, err := amqp.Dial("amqp://guest:guest@rabbitmq3:5672/")
	failOnError(err, "Failed to connect to RabbitMQ")
	defer conn.Close()

	ch, err := conn.Channel()
	failOnError(err, "Failed to open a channel")
	defer ch.Close()

	q, err := ch.QueueDeclare(
		"go-to-php", // name
		false,       // durable
		false,       // delete when unused
		false,       // exclusive
		false,       // no-wait
		nil,         // arguments
	)
	failOnError(err, "Failed to declare a queue")
	ctx, cancel := context.WithTimeout(context.Background(), 5*time.Second)
	defer cancel()

	body := Calculate()
	err = ch.PublishWithContext(ctx,
		"",     // exchange
		q.Name, // routing key
		false,  // mandatory
		false,  // immediate
		amqp.Publishing{
			ContentType: "text/plain",
			Body:        []byte(body),
		})
	ch.Close()
	failOnError(err, "Failed to publish a message")
	log.Printf(" [x] Sent %s\n", body)
}
