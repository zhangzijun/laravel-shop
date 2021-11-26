<?php
/**
 * Copyright (c) 2020,2345
 * 摘    要：
 * 作    者：张子骏
 * 修改日期：2021-10-26 10:07
 */

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class MqTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicProducer()
    {
        $conf = [
            'host' => '172.17.110.74',
            'port' => 5672,
            'user' => 'admin',
            'pwd' => 'admin',
            'vhost' => 'mq_vhost'
        ];
        $exchangeName = 'mail_send_ex'; //交换机名
        $queueName = 'testmail'; //队列名称
        $routingKey = 'mail_send'; //路由关键字(也可以省略)
        $conn = new AMQPStreamConnection($conf['host'], $conf['port'], $conf['user'], $conf['pwd'], $conf['vhost']);
        $channel = $conn->channel();
        //$channel = $connect->channel();
//确认投放队列,并将队列持久化
//        $que = new \AMQPQueue();
        $channel->queue_declare($queueName, false, true, false, false);
//异步回调消息确认
        $channel->set_ack_handler(
            function (AMQPMessage $message) {
                echo '消息确认内容' . $message->body . PHP_EOL;
            }
        );
//异步回调,消息丢失处理
        $channel->set_nack_handler(
            function (AMQPMessage $message) {
                echo '消息丢失' . $message->body . PHP_EOL;
            }
        );
//开启消息确认
        $channel->confirm_select();
        for ($i = 0; $i < 100; $i++) {
            $pushData = "hello world---------$i";
            $msg = new AMQPMessage($pushData);
            $channel->basic_publish($msg, '', $queueName);
            echo $pushData . PHP_EOL;
        }
//阻塞等待消息确认 监听成功或失败返回结束
        $channel->wait_for_pending_acks();

        $channel->close();
        //$connect->close();
//        $channel->exchange_declare($exchangeName, 'direct', false, true, false);
//        $channel->queue_declare($queueName, false, true, false, false);
//        $channel->queue_bind($queueName, $exchangeName, $routingKey);
//        $messageBody = json_encode(['name' => 'iGoo', 'age'=> 22]);
//        $message = new AMQPMessage($messageBody, ['content_type' => 'text/plain', 'delivery_mode' => 2]);
//        $r = $channel->basic_publish($message, $exchangeName, $routingKey);
        // $channel->close();
        // $conn->close();
        $this->assertTrue(true);
    }

    public function testBasicConsumer()
    {
        $conf = [
            'host' => '172.17.110.74',
            'port' => 5672,
            'user' => 'admin',
            'pwd' => 'admin',
            'vhost' => 'mq_vhost'
        ];
        $consumerTag = 'consumer';
        $queueName = 'testmail';
        $exchangeName = 'mail_send_ex'; //交换机名
        $routingKey = 'mail_send'; //路由关键字(也可以省略)
        $conn = new AMQPStreamConnection($conf['host'], $conf['port'], $conf['user'], $conf['pwd'], $conf['vhost']);
        $channel = $conn->channel();
        echo '创建队列成功' . PHP_EOL;
        $callback = function ($msg) {
            echo '接收到消息' . $msg->body . PHP_EOL;
            //$msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        };
        $channel->basic_consume($queueName, '', false, true, false, false, $callback);
        while ($channel->is_consuming()) {
            $channel->wait();
        }
        $channel->close();
        $conn->close();
//        $ret = $channel->basic_consume($queueName, '', false, false, false, false, function ($msg){
//            var_export($msg->body);
//            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
//        });
//        $channel->exchange_declare($exchangeName, 'direct', false, true, false);
//        $channel->queue_declare($queueName, false, true, false, false);
//        $channel->queue_bind($queueName, $exchangeName, $routingKey);
//        $messageBody = json_encode(['name' => 'iGoo', 'age'=> 22]);
//        $message = new AMQPMessage($messageBody, ['content_type' => 'text/plain', 'delivery_mode' => 2]);
//        $r = $channel->basic_publish($message, $exchangeName, $routingKey);
        // $channel->close();
        // $conn->close();
        $this->assertTrue(true);
    }
}
