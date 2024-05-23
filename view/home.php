<?php
require('../application.php');


$customer_id = 40;


$paymentService = new PaymentService();
$paymentController = new PaymentController($paymentService);
$session_payment = $paymentController->handlePaymentRequest($customer_id);


?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>PayPlug Lightbox example</title>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<link href="//netdna.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.css" rel="stylesheet">


		


<script type="text/javascript" src="https://api.payplug.com/js/1/form.latest.js"></script>

<script type="text/javascript">
  document.addEventListener('DOMContentLoaded', function() {
    [].forEach.call(document.querySelectorAll("#signupForm"), function(el) {

      // check if cgu is checked
      el.addEventListener('submit', function(event) {
        if (!document.getElementById('cgu').checked ) {
          alert('<?= 'Vous devez accepter les Conditions Générales de Garantie' ?>');
          event.preventDefault();
        } 
        // si checked card
        else if (document.getElementById('card').checked){
          var payplug_url = '<?= $session_payment->hosted_payment->payment_url ?>';
        Payplug.showPayment(payplug_url);
        event.preventDefault();
        }
      })
    })
  })


  
</script>


</head>




<body class="warranty" class="bg-dark">

  
  
  <div class="body-content">
    
    <section class="content-form ">
      <header class="page-header"><h1><?php echo 'PAIEMENT';?></h1></header>
    </section>

    <section class="container">
      <div class="text-center lightGray" style="font-size: 22px;">
        <p>
          <?php echo 'Vous avez choisi la livraison à domicile par Colissimo à 6.90€.';?> 
        </p>
      </div>

      <div class="row">
        <div class='col-md-4 mx-auto'>
            <form action="" method="post" id="signupForm" onsubmit="return false" class="formulaire" novalidate>
              <div class="p-2">

                <label for="card" class="d-flex">
                  <div class="">
                    <input type="radio" name="payment" id="card" value="payplug" />
                    <span><?= 'Payer par carte bancaire' ?></span>
                    <div class="d-flex" style="margin-left: 15px;">
                      <img src="https://via.placeholder.com/100" alt="" class="visa" width="30px" height="30px">
                      <img src="https://via.placeholder.com/100" alt="" class="mastercard" width="30px" height="30px">
                      <img src="https://via.placeholder.com/100" alt="" class="mastro" width="30px" height="30px">
                      <img src="https://via.placeholder.com/100" alt="" class="CB" width="30px" height="30px">
                    </div>
                  </div>
                  <br>
                </label>
            
            
                <label for="paypal" class="d-flex" >
                  <div class="">
                    <input type="radio" name="payment" id="paypal" value="paypal" />
                    <span><?= ('Payer avec Paypal') ?></span>
                    <div class="d-flex" style="margin-left: 15px;">
                      <img src="https://via.placeholder.com/100" alt="" class="paypal" width="30px" height="30px">
                    </div>
                  </div>
                  <br>
                </label>
              </div>

              <div class="">
                <input type="checkbox" name="cgu" id="cgu" required />
                <label name="cgu"> 
                  <?= 'J\'accepte les Conditions Générales de Garantie' ?>
                </label>

              </div>
              
              <div class="d-flex gap-1">
                <a class="btn btn-primary h6 my-5 px-5 py-2 btn-style1 col-md-6" 
										href="#">
                      RETOUR
                  </a>

                <button type="submit" class="btn btn-primary h6 my-5 px-5 py-2 btn-style1 col-md-6">PAIEMENT</button>
                
                </div>

            </form>
        </div>
      </div>

    </section>
  </div>
</body>
</html>