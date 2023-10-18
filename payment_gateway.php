<?php
    include 'vendor/autoload.php';
    $amazonpay_config = array(
        'public_key_id' => 'SANDBOX-AHKKYPXOEUUN2XJUKXKWDEJ2',
        'private_key'   => 'AmazonPay_SANDBOX-AHKKYPXOEUUN2XJUKXKWDEJ2.pem',
        'region'        => 'US',
        'algorithm'     => 'AMZN-PAY-RSASSA-PSS-V2',
    );

    $payload = array(
        "webCheckoutDetails" => array(
            "checkoutReviewReturnUrl" => "https://usartframes.com/amazon-test/testing/testing_url.php"
        ),
        "storeId" => "amzn1.application-oa2-client.fc230909a9c44b9c9b77355c6ac6696f",
        "scopes" => array(
            "name",
            "email",
            "phoneNumber",
            "billingAddress"
        ),
        "deliverySpecifications" => array(
            "specialRestrictions" => array(
                "RestrictPOBoxes"
            ),
            "addressRestrictions" => array(
                "type" => "Allowed",
                "restrictions" => array(
                    "US" => (object) array(
                        "statesOrRegions" => array(
                            "WA"
                        ),
                        "zipCodes" => array(
                            "95050",
                            "93405"
                        )
                    ),
                    "GB" => (object) array(
                        "zipCodes" => array(
                            "72046",
                            "72047"
                        )
                    ),
                    "IN" => (object) array(
                        "statesOrRegions" => array(
                            "AP"
                        )
                    ),
                    "JP" => (object) array()
                )
            )
        )
    );

    $headers = array('x-amz-pay-Idempotency-Key' => uniqid());
    try {
        $client = new Amazon\Pay\API\Client($amazonpay_config);
        $result = $client->createCheckoutSession($payload, $headers);
        $signature = $client->generateButtonSignature($payload);

        if ($result['status'] === 201) {
            // created
            $response = json_decode($result['response'], true);
            $checkoutSessionId = $response['checkoutSessionId'];
            
        } else {
            // check the error
            echo 'status=' . $result['status'] . '; response=' . $result['response'] . "\n";
        }
    } catch (\Exception $e) {
        // handle the exception
        echo $e . "\n";
    }
    $payload = json_encode($payload);

    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Amazon Pay</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your external CSS file -->
    <script src="https://static-na.payments-amazon.com/checkout.js"></script>

</head>
<body>
    <div class="container">
        <form action="" method="post">
        <div id="AmazonPayButton"></div>
        <script src="https://static-na.payments-amazon.com/checkout.js"></script>
        <script type="text/javascript" charset="utf-8">

            const amazonPayButton = amazon.Pay.renderButton('#AmazonPayButton', {
                // set checkout environment
                merchantId: 'AQTWPY668NPNO',
                publicKeyId: 'SANDBOX-AHKKYPXOEUUN2XJUKXKWDEJ2',
                ledgerCurrency: 'USD',              
                // customize the buyer experience
                checkoutLanguage: 'en_US',
                productType: 'PayAndShip',
                placement: 'Cart',
                buttonColor: 'Gold',
                estimatedOrderAmount: { "amount": "109.99", "currencyCode": "USD"},
                // configure Create Checkout Session request
                createCheckoutSessionConfig: {                     
                    payloadJSON: '<?php echo $payload ?>', // payload generated in step 2
                    signature: '<?php echo $signature ?>', // signature generatd in step 3
                    algorithm: 'AMZN-PAY-RSASSA-PSS-V2'
                }   
            });
            
        </script>
    </div> 

</body>
</html>

