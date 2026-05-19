<?php

namespace App\Core;

class MoMoPayment
{
    public static function createPayment($orderId, $orderInfo, $amount, $requestId, $returnUrl = null, $extraData = "")
    {
        $partnerCode = MOMO_PARTNER_CODE;
        $accessKey = MOMO_ACCESS_KEY;
        $secretKey = MOMO_SECRET_KEY;
        
        // MoMo v2 yêu cầu IPN URL (thay cho notifyUrl)
        $ipnUrl = MOMO_NOTIFY_URL; 
        $redirectUrl = $returnUrl ?? MOMO_RETURN_URL;
        $requestType = "captureWallet";
        $orderId = $orderId . "_" . time(); 

        // QUAN TRỌNG: Thứ tự tham số trong chuỗi hash PHẢI theo bảng chữ cái
        // accessKey, amount, extraData, ipnUrl, orderId, orderInfo, partnerCode, redirectUrl, requestId, requestType
        $rawHash = "accessKey=" . $accessKey .
            "&amount=" . $amount .
            "&extraData=" . $extraData .
            "&ipnUrl=" . $ipnUrl .
            "&orderId=" . $orderId .
            "&orderInfo=" . $orderInfo .
            "&partnerCode=" . $partnerCode .
            "&redirectUrl=" . $redirectUrl .
            "&requestId=" . $requestId .
            "&requestType=" . $requestType;

        $signature = hash_hmac("sha256", $rawHash, $secretKey);

        $data = array(
            'partnerCode' => $partnerCode,
            'partnerName' => "Quan Ly San Bong",
            'storeId' => "MomoStore",
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl' => $ipnUrl,
            'lang' => 'vi',
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature
        );

        $result = self::execPostRequest(MOMO_ENDPOINT, json_encode($data));
        return json_decode($result, true);
    }

    private static function execPostRequest($url, $data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
        );
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        
        $result = curl_exec($ch);
        
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            error_log("MoMo Curl Error: " . $error_msg);
        }
        
        curl_close($ch);
        return $result;
    }
}
