# 1. Install
PHP Sdk for FPT
Run:

    composer require beobeboi/sms-sdk

# 2. Config

For Laravel, run:

    php artisan vendor:publish --provider="DiagVN\\SmsServiceProvider"

Run

    php artisan migrate

Add config to .env

    FPT_BRAND_NAME=
    FPT_CLIENT_ID=
    FPT_CLIENT_SECRET=
    FPT_MODE=
    SMS_PROVIDER=
    SMS_WHITELIST=
    LOG_SMS=

SMS_PROVIDER: SMS service provider name

SMS_WHITELIST: List phone number was allowed to send SMS in test and dev enviroment

LOG_SMS: true/false, set enable log sms

# 3. Example

    use DiagVN\SmsService;
    try {
        $service = app(SmsService::class);
        $service->sendMessage(
            +840909111111,
            'Test Send SMS'
        );
    } catch (Exception $ex) {
        report($ex);
    }
