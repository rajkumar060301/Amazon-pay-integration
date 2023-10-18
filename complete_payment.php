<?php
    include 'vendor/autoload.php';
    $sessionId = $_GET['amazonCheckoutSessionId'];

    $amazonpay_config = array(
        'public_key_id' => 'SANDBOX-AHKKYPXOEUUN2XJUKXKWDEJ2',
        'private_key'   => 'AmazonPay_SANDBOX-AHKKYPXOEUUN2XJUKXKWDEJ2.pem',
        'region'        => 'US',
        'algorithm'     => 'AMZN-PAY-RSASSA-PSS-V2',
    );
        // Complete payment 
    $payload = array(
        'chargeAmount' => array(
            'amount' => '10',
            'currencyCode' => 'USD'
        )
    );

    try {
        $client = new Amazon\Pay\API\Client($amazonpay_config);
        $result = $client->completeCheckoutSession($sessionId, $payload);

        if ($result['status'] === 202) {
            // Charge Permission is in AuthorizationInitiated state
            $response = json_decode($result['response'], true);
            $checkoutSessionState = $response['statusDetails']['state'];
            $chargeId = $response['chargeId'];
            $chargePermissionId = $response['chargePermissionId'];
        } 
        else if ($result['status'] === 200) {
            $response = json_decode($result['response'], true);
            $checkoutSessionState = $response['statusDetails']['state'];
            $chargePermissionId = $response['chargePermissionId'];
            
            echo "<pre>";
            print_r($response);
            echo "</pre>";
            
        } else {
            // check the error
            echo 'status=' . $result['status'] . '; response=' . $result['response'] . "\n";
        }
    } catch (Exception $e) {
        // handle the exception
        echo $e;
    }
?>