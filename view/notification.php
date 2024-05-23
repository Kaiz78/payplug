<?php

require('../application.php');


ob_start();



$input = file_get_contents('php://input');
try {
  $resource = \Payplug\Notification::treat($input);


  if ($resource instanceof \Payplug\Resource\Payment
    && $resource->is_paid 
    // Ensure that the payment was paid<
    ) {


    $paymentController = new PaymentController($paymentService);


    $file = 'log.txt';
    $current = file_get_contents($file);
    $current .= "Paiement effectué : " . $resource->id . "\n";
    $current .= "customer : " .  $resource->metadata['customer_id']  . "\n";
    $current .= "email : " .  $resource->metadata['email'] . "\n";
    
    var_dump($resource);
    
    $output = ob_get_contents();
    ob_end_clean();
    $current .= $output . "\n";
    file_put_contents($file, $current);




    // update the payment status
    $update = $paymentController->handleNotification();



  } else if ($resource instanceof \Payplug\Resource\Refund) {
    
    $file = 'log.txt';
    $current = file_get_contents($file);
    $current .= "Paiement remboursé : " . $resource->id . "\n";
    $current .= "customer : " .  $resource->metadata['customer_id']  . "\n";
    $current .= "email : " .  $resource->metadata['email'] . "\n";
    
    var_dump($resource);
    
    $output = ob_get_contents();
    ob_end_clean();
    $current .= $output . "\n";
    file_put_contents($file, $current);


  }
}
catch (\Payplug\Exception\PayplugException $exception) {
 // Handle errors

  var_dump($exception->getMessage());
  $file = 'log.txt';
  $current = file_get_contents($file);
  $current .= "Paiement échec : " . $resource->id . "\n";
  $current .= "customer : " .  $resource->metadata['customers_id'] . "\n";
  $current .= "email : " .  $resource->metadata['email'] . "\n";

  $output = ob_get_contents();
  ob_end_clean();
  $current .= $output . "\n";
  file_put_contents($file, $current);

}