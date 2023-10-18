
<?php
    include 'vendor/autoload.php';
    $sessionId = $_GET['amazonCheckoutSessionId'];

    $amazonpay_config = array(
        'public_key_id' => 'SANDBOX-AHKKYPXOEUUN2XJUKXKWDEJ2',
        'private_key'   => 'AmazonPay_SANDBOX-AHKKYPXOEUUN2XJUKXKWDEJ2.pem',
        'region'        => 'US',
        'algorithm'     => 'AMZN-PAY-RSASSA-PSS-V2',
    );
    
    
    // Get checkout session
        
    try {
        $client = new Amazon\Pay\API\Client($amazonpay_config);
        $result = $client->getCheckoutSession($sessionId);
        if ($result['status'] === 200) {
 
            $response = json_decode($result['response'], true);
            
            $checkoutSessionState = $response['statusDetails']['state'];
            $chargeId = $response['chargeId'];
            $chargePermissionId = $response['chargePermissionId'];

            // NOTE: Once Checkout Session moves to a "Completed" state, buyer and shipping
            // details must be obtained from the getChargePermission() function call instead
            $buyerName = $response['buyer']['name'];
            $buyerAddress = $response['buyer']['shippingAddress'];
        } else {
            // check the error
            echo 'status=' . $result['status'] . '; response=' . $result['response'] . "\n";
        }
    } catch (Exception $e) {
        // handle the exception
        echo $e;
    }
    
        // update checkout
    
       $payload = array(
        'webCheckoutDetails' => array(
            'checkoutResultReturnUrl' => 'https://usartframes.com/amazon-test/testing/payment_gateway.php'
        ),
        'paymentDetails' => array(
            'paymentIntent' => 'AuthorizeWithCapture',
            'canHandlePendingAuthorization' => false,
            'softDescriptor' => 'Descriptor',
            'chargeAmount' => array(
                'amount' => '10',
                'currencyCode' => 'USD'
            )
        ),
        'merchantMetadata' => array(
            'merchantReferenceId' => 'SANDBOX-AHKKYPXOEUUN2XJUKXKWDEJ2',
            'merchantStoreName' => 'Merchant store name',
            'noteToBuyer' => 'Note to buyer',
            'customInformation' => 'Custom information'
        )
    );

    try {
        $client = new Amazon\Pay\API\Client($amazonpay_config);

        $result = $client->updateCheckoutSession($sessionId, $payload);
        // echo "<pre>";
        // print_r($result);
        // echo "</pre>";

        if ($result['status'] === 200) {
            
            $response = json_decode($result['response'], true);
            $amazonPayRedirectUrl = $response['webCheckoutDetails']['amazonPayRedirectUrl'];
            
            echo "<script>window.location.href = '$amazonPayRedirectUrl'</script>";

        } else {
            // check the error
            echo 'status=' . $result['status'] . '; response=' . $result['response'];
        }
    } catch (Exception $e) {
        // handle the exception
        echo $e;
    }
    


?>
