<?php

namespace DiagVN;

interface SmsClient
{
    /**
     * @param string $phone
     * @param string $message
     * @return mixed
     * @throws \Exception
     */
    public function send(string $phone, string $message);

    public function formatPhoneNumber(string $phone): string;
}
