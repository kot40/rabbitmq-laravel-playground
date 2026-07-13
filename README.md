<div align="center" style="display: flex; align-items: center; justify-content: center; gap: 40px;">
  <a href="https://laravel.com" target="_blank">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" 
         width="340" 
         alt="Laravel Logo">
  </a>
  <a href="https://www.rabbitmq.com" target="_blank">
    <img src="https://www.rabbitmq.com/img/rabbitmq-logo-by-tanzu.svg" 
         width="400" 
         alt="RabbitMQ Logo">
  </a>
</div>

# RabbitMQ - Laravel Playground

Hands-on exploration of RabbitMQ integration with Laravel.

## What is covered

**Topic Exchange with routing keys**
- Single exchange routing messages to multiple queues by routing key pattern
- Separate queues for emails, notifications, orders, analytics

**Microservice connections**
- Dedicated RabbitMQ connections per microservice (orders, invoices, delivery)
- Each service has its own exchange with topic type and durable queues
- Inter-service communication via routing keys (ms.orders.invoice-updates etc.)

**Laravel Horizon over RabbitMQ**
- Horizon configured with RabbitMQ as the queue driver instead of default Redis
- Separate supervisors per queue with individual settings:
  process count, timeout, tries, memory limit
- Monitoring job execution, timing, and failed messages via Horizon UI

## Stack
PHP - Laravel - RabbitMQ - Horizon

## Why this repo
RabbitMQ is a common requirement in distributed systems and microservice
architectures. This playground covers AMQP-based queue patterns.
