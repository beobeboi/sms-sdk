<?php

namespace DiagVN;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SmsService
{
    public function sendMessage(
        string $number,
        string $message,
        bool $isCheckWhiteList = false,
        string $provider = null
    ): ?array {
        try {
            if (!$provider) {
                $provider = config('sms.provider');
            }
            $service = app('sms')->get($provider);
            $number = $service->formatPhoneNumber($number);
            $canSendMessage = $this->canSendMessage($number);
            if ($isCheckWhiteList && !$canSendMessage) {
                Log::error("Phone: " . $number . ' not in whitelist');
                return null;
            }
            $response = $service->send($number, $message);
            if (config('sms.log_sms')) {
                Log::debug("Phone: " . $number, ['message' => $message, 'FPT Response' => $response]);
            }

            $logSmsId = $this->logSms($number, $response);
            $response['is_success'] = true;
            if ($logSmsId) {
                $response['log_sms_id'] = $logSmsId;
            }

            return $response;
        } catch (\Exception $ex) {
            Log::error("Can not send message FPT: " . $number, ['message' => $message, 'error' => $ex->getMessage()]);
            report($ex);
            $logSmsId = $this->logSms(
                $number, [
                'message' => $message,
                'error' => $ex->getMessage(),
            ],
                false
            );
            return $logSmsId ? ['log_sms_id' => $logSmsId, 'is_success' => false] : ['is_success' => false];
        }
    }

    private function canSendMessage(string $number): bool
    {
        $whitelist = config('sms.whitelist');
        if ($whitelist && strpos($whitelist, $number) === false) {
            return false;
        }

        return true;
    }

    private function logSms(
        string $number,
        array $response,
        bool $isSuccess = true
    ): int {
        if (config('sms.log_sms')) {
            return DB::table('sms_logs')->insertGetId([
                'phone_number' => $number,
                'is_success' => $isSuccess,
                'response' => json_encode($response),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        return 0;
    }
}
