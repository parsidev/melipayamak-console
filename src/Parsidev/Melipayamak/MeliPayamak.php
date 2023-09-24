<?php

namespace Parsidev\Melipayamak;

use Parsidev\Melipayamak\Enums\MessageType;
use Parsidev\Melipayamak\Enums\Method;

class MeliPayamak
{
    const BASE = "https://console.melipayamak.com/api";

    protected string $token;
    protected string $from;

    public function __construct(string $token, string $from)
    {
        $this->token = $token;
        $this->from = $from;
    }

    protected function send(string $url, array $data, Method $method): mixed
    {
        $data_string = json_encode($data);
        $header = [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string)
        ];

        $ch = curl_init(sprintf("%s/%s/%s", self::BASE, $url, $this->token));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method->value);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $result = curl_exec($ch);
        curl_close($ch);

        if (!$result) {
            return [];
        }

        return json_decode($result);
    }

    public function SendOTP(string $to): mixed
    {
        $data = [
            "to" => $to
        ];

        return $this->send("send/otp", $data, Method::POST);
    }

    public function SendSchedule(string $message, string $to, string $date, int $period): mixed
    {
        $data = [
            "message" => $message,
            "from" => $this->from,
            "to" => $to,
            "date" => $date,
            "period" => $period
        ];

        return $this->send("send/schedule", $data, Method::POST);
    }

    public function SendSimple(string $message, string $to): mixed
    {
        $data = [
            "from" => $this->from,
            "to" => $to,
            "text" => $message
        ];

        return $this->send("send/simple", $data, Method::POST);
    }

    public function SendAdvance(string $message, array $to, string $udh = ""): mixed
    {
        $data = [
            "from" => $this->from,
            "text" => $message,
            "to" => $to,
            "udh" => $udh
        ];

        return $this->send("send/advanced", $data, Method::POST);
    }

    public function SendShared(int $smsId, string $to, array $args): mixed
    {

        $data = [
            "bodyId" => $smsId,
            "to" => $to,
            "args" => $args
        ];

        return $this->send("send/shared", $data, Method::POST);
    }

    public function SendDomain(string $message, string $to, string $domain): mixed
    {
        $data = [
            "to" => $to,
            "text" => $message,
            "domain" => $domain,
            "from" => $this->from
        ];

        return $this->send("send/withdomain", $data, Method::POST);
    }

    public function SendMultiple(array $message, array $to, string $udh = ""): mixed
    {
        $data = [
            "from" => $this->from,
            "to" => $to,
            "text" => $message,
            "udh" => $udh
        ];

        return $this->send("send/multiple", $data, Method::POST);
    }

    public function ReceiveStatus(array $recIds): mixed
    {
        $data = [
            "recIds" => $recIds
        ];

        return $this->send("receive/status", $data, Method::POST);
    }

    public function ReceiveMessages(MessageType $type, string $number = "", int $index = 0, int $count = 100): mixed
    {
        $data = [
            "type" => $type->value,
            "number" => $number,
            "index" => $index,
            "count" => $count
        ];

        return $this->send("receive/messages", $data, Method::POST);
    }

    public function ReceiveInboxCount(bool $isRead): mixed
    {
        $data = [
            "isRead" => $isRead
        ];

        return $this->send("receive/inboxcount", $data, Method::POST);
    }

    public function ReceiveCredit(): mixed
    {
        return $this->send("receive/credit", [], Method::GET);
    }

    public function ReceivePrice(int $mci, int $mtn, string $message): mixed
    {
        $data = [
            "mtnCount" => $mci,
            "irancellCount" => $mtn,
            "from" => $this->from,
            "text" => $message
        ];

        return $this->send("receive/price", $data, Method::POST);
    }

}
