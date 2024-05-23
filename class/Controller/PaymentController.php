<?php

use Payplug\Notification;

class PaymentController
{
    private $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function handlePaymentRequest($customer_id)
    {

        $this->paymentService->setCustomersId($customer_id);

        $paymentData = [
            'amount' => 690, // Montant en centime
            'currency' => 'EUR',
            'billing' => [
                'title' => 'Mr',
                'first_name' => 'ss',
                'last_name' => 'sss',
                'email' => 's@bigben-connected.com',
                'address1' => 'Rue de la Victoire',
                'postcode' => '75009',
                'city' => 'Paris',
                'country' => 'FR',
                'language' => 'fr'
            ],
            'shipping' => [
                'title' => 'Mr',
                'first_name' => 'ss',
                'last_name' => 'sss',
                'email' => 's@bigben-connected.com',
                'address1' => 'Rue de la Victoire',
                'postcode' => '75009',
                'city' => 'Paris',
                'country' => 'FR',
                'language' => 'fr',
                'delivery_type' => 'BILLING'
            ],

            'hosted_payment' => [
                'return_url' => 'http://localhost/projet/payplug/view/success.php',
                'cancel_url' => 'http://localhost/projet/payplug/view/cancel.php'
            ],
            'notification_url' => 'http://localhost/projet/payplug/view/notification.php',

            'metadata' => [
                'customers_id' => 42 ,
                'email' => 's@bigben-connected.com'
            ]
        ];

        try {
            $payment = $this->paymentService->createOrUpdatePayment($paymentData);
            return $payment;
        } catch (\Exception $e) {
            echo $e->getMessage();
            die;
        }
    }

    public function handleNotification(){
        $input = file_get_contents('php://input');
        
        try {
            $resource = Notification::treat($input);    
            $this->paymentService->handleNotification($resource);
        } catch (\Exception $e) {
            echo $e->getMessage();
            die;
        }
    }
}
