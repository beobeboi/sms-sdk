<?php

namespace DiagVN\Services\Fpt;

use DiagVN\Services\Fpt\TechAPI\Api\SendBrandnameOtp;
use DiagVN\Services\Fpt\TechAPI\Auth\AccessToken;
use DiagVN\Services\Fpt\TechAPI\Auth\ClientCredentials;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use DiagVN\Services\Fpt\TechAPI\Client;
use DiagVN\Services\Fpt\TechAPI\Constant;
use DiagVN\Services\Fpt\TechAPI\Exception;
use DiagVN\SmsClient;

class FptClient implements SmsClient
{
    /** @var string[] */
    private $config;

    /** @var string[] */
    private $credentials;

    /**
     * FtpClient constructor.
     * @param string[] $credentials
     * @param string[] $options
     */
    public function __construct($credentials, $options)
    {
        $this->config = $options;
        $this->credentials = $credentials;

        // config api
        Constant::configs([
            'mode' => $options['sms.fpt.mode'],
            'connect_timeout' => $options['sms.fpt.timeout'],
            'enable_cache' => $options['sms.enable_cache'],
        ]);
    }


    /**
     * Get Fpt authorization
     * @return ClientCredentials
     */
    private function getTechAuthorization()
    {
        $client = new Client(
            $this->credentials['client_id'],
            $this->credentials['client_secret'],
            (array)$this->config['sms.fpt.scopes']
        );

        return new ClientCredentials($client);
    }


    /**
     * @param string $phone
     * @param string $message
     * @return mixed
     * @throws \Exception
     */
    public function send(string $phone, string $message)
    {
        if (empty($phone))
            throw new \Exception("Please provide phone number");

        if (empty($message))
            throw new \Exception("Please provide message");

        $arrMessage = [
            'Phone' => $phone,
            'BrandName' => $this->config['sms.fpt.brand_name'],
            'Message' => $message,
        ];

        $apiSendBrandName = new SendBrandnameOtp($arrMessage);

        try {
            $oGrantType = $this->getTechAuthorization();
            $arrResponse = $oGrantType->execute($apiSendBrandName);

            if (!empty($arrResponse['error'])) {
                AccessToken::getInstance()->clear();

                throw new Exception($arrResponse['error_description'], $arrResponse['error']);
            }

            return $arrResponse;
        } catch (\Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    public function formatPhoneNumber(string $number): string
    {
        $phoneUtil = PhoneNumberUtil::getInstance();
        $swissNumberProto = $phoneUtil->parse($number, config('sms.country_code'));
        $nationalPhone = $phoneUtil->format($swissNumberProto, PhoneNumberFormat::INTERNATIONAL);
        $countryCode = $phoneUtil->getCountryCodeForRegion(config('sms.country_code'));
        if (strpos($nationalPhone, '0') === 0) {
            $nationalPhone = $countryCode . mb_substr($nationalPhone, 1, strlen($nationalPhone) - 1);
        }
        $number = str_replace('+', '', $nationalPhone);
        return str_replace(' ', '', $number);
    }
}
