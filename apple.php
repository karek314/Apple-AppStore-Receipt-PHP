<?php

function getReceiptData($receipt,$sandbox) {
  if ($sandbox == 1) {
    $endpoint = 'https://sandbox.itunes.apple.com/verifyReceipt';
  } else {
    $endpoint = 'https://buy.itunes.apple.com/verifyReceipt';
  }
  $postData = json_encode(
    array('receipt-data' => $receipt)
  );
  $ch = curl_init($endpoint);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
  curl_setopt($ch, CURLOPT_TIMEOUT,6);
  $response = curl_exec($ch);
  $retry = 0;
  while(curl_errno($ch) == 28 && $retry < 4){
    $response = curl_exec($ch);
    $retry++;
  }
  curl_close($ch);
  $data = json_decode($response, true);
  $r_data = array();
  if ($data) {
    if (isset($data['status'])) {
      $r_data['status'] = $data['status'];
    } else {
      return false;
    }
    if (isset($data['receipt'])) {
      $receipt = $data['receipt'];
      if ($receipt['bid']) {
        $r_data['bundle_id'] = $receipt['bid'];
      }
      if ($receipt['product_id']) {
          $r_data['product_id'] = $receipt['product_id'];
      }
      if ($receipt['transaction_id']) {
        $r_data['transaction_id'] = $receipt['original_transaction_id'];
      }
      if ($receipt['purchase_date_ms']) {
        $r_data['purchase_date'] = $receipt['purchase_date_ms']/1000;
      }
      if ($receipt['bundle_id']) {
        $r_data['bundle_id'] = $receipt['bundle_id'];
      }
      if (isset($receipt['in_app'][0])) {
        $in_app = $receipt['in_app'][0];
        if ($in_app['product_id']) {
          $r_data['product_id'] = $in_app['product_id'];
        }
        if ($in_app['transaction_id']) {
          $r_data['transaction_id'] = $in_app['transaction_id'];
        }
        if ($in_app['original_purchase_date_ms']) {
          $r_data['purchase_date'] = $in_app['original_purchase_date_ms']/1000;
        }
      }
    } else {
      return false;
    }
    return $r_data;
  } else {
    return false;
  }
}


const RESULT_OK = 0;

// The App Store could not read the JSON object you provided.
const RESULT_APPSTORE_CANNOT_READ = 21000;

// The data in the receipt-data property was malformed or missing.
const RESULT_DATA_MALFORMED = 21002;

// The receipt could not be authenticated.
const RESULT_RECEIPT_NOT_AUTHENTICATED = 21003;

// The shared secret you provided does not match the shared secret on file for your account.
// Only returned for iOS 6 style transaction receipts for auto-renewable subscriptions.
const RESULT_SHARED_SECRET_NOT_MATCH = 21004;

// The receipt server is not currently available.
const RESULT_RECEIPT_SERVER_UNAVAILABLE = 21005;

// This receipt is valid but the subscription has expired. When this status code is returned to your server, the receipt data is also decoded and returned as part of the response.
// Only returned for iOS 6 style transaction receipts for auto-renewable subscriptions.
const RESULT_RECEIPT_VALID_BUT_SUB_EXPIRED = 21006;

// This receipt is from the test environment, but it was sent to the production environment for verification. Send it to the test environment instead.
// special case for app review handling - forward any request that is intended for the Sandbox but was sent to Production, this is what the app review team does
const RESULT_SANDBOX_RECEIPT_SENT_TO_PRODUCTION = 21007;

// This receipt is from the production environment, but it was sent to the test environment for verification. Send it to the production environment instead.
const RESULT_PRODUCTION_RECEIPT_SENT_TO_SANDBOX = 21008;


?>
