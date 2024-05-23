<?php



use Payplug\Payplug;
use Payplug\Payment;


class PaymentService
{
    private $secretKey = null;
    private $customers_id = null;
    private $dataBase;

    /**  
     * @param Database $dataBase Facultatif, si non renseigné, on instancie un objet Database 
     */
    public function __construct($dataBase = null)
    {
        if ($dataBase instanceof Database) {
            $this->dataBase = $dataBase;
        } else {
            $this->dataBase = new Database();
        }

        Payplug::setSecretKey(SECRET_KEY_PAYPLUG_TEST);
    }





    public function setCustomersId($customers_id)
    {
        $this->customers_id = $customers_id;
    }


    protected function getPaymentId()
    {
        try {
            // Connexion à la base de données
            $get_payment = $this->dataBase->select('payment', null, ['customers_id' => $this->customers_id, 'status' => 'pending']);



            if (empty($get_payment)) {
                return null;
            }


            $payment_id = $get_payment[0]['payment_id'];
            return $payment_id;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }


    protected function setPaymentId($id)
    {
        $insert = $this->dataBase->insert('payment', ['customers_id' => $this->customers_id, 'payment_id' => $id, 'status' => 'pending', 'date_added' => date('Y-m-d H:i:s'), 'last_modified' => date('Y-m-d H:i:s')]);
        return $insert;
    }


    public function createOrUpdatePayment($paymentData)
    {
        try {
            $paymentId = $this->getPaymentId();

            // Si un paiement est en cours
            if ($this->isPaymentPending($paymentId)) {
                $payment = Payment::retrieve($paymentId);              
            } else {
                $payment = Payment::create($paymentData);
                // Insertion du paiement dans la base de données
                $this->setPaymentId($payment->id);
            }

            return $payment;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }


    protected function isPaymentPending($paymentId)
    {

        // si $paymentId est null
        if (is_null($paymentId)) {
            return false;
        }

        $payment = Payment::retrieve($paymentId);

        //  check si le paiement est terminée
        if ($payment->failure || $payment->is_paid) {
            return false;
        } else {
            return true;
        }
    }


    // Notification
    public function handleNotification($resource)
    {
        try {
            if (
                $resource instanceof \Payplug\Resource\Payment
                && $resource->is_paid
                // Ensure that the payment was paid<
            ) {
                // update the payment status
                $update = $this->dataBase->update('payment', ['status' => 'paid', 'last_modified' => date('Y-m-d H:i:s')], ['payment_id' => $resource->id]);

                // get customers_id
                $resource->metadata['customer_id'];

                // create orders


                return $update;
            } else if ($resource instanceof \Payplug\Resource\Refund) {

                // update the payment status
                $update = $this->dataBase->update('payment', ['status' => 'refunded', 'last_modified' => date('Y-m-d H:i:s')], ['payment_id' => $resource->id]);

                return $update;
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
