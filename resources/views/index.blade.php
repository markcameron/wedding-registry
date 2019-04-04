@extends('layouts.main')

@section('content')

  <div class="hero-area">
    <!-- Start Hero Slider -->
    <div class="flexslider heroflex hero-slider" data-autoplay="yes" data-pagination="no" data-arrows="yes" data-style="fade" data-pause="yes">
      <ul class="slides">
        <li class="parallax" style="background-image:url(/images/photo-1.jpeg)">
          <div class="flex-caption">
            <div class="container">
              <div class="flex-caption-table">
                <div class="flex-caption-cell">
                  <div class="flex-caption-text">
                    <h2>Rachel and Mark Honeymoon Fund</h2>
                    <p>As far as material things go, we are covered and do not need anything more. However if you want to get us something we would glady accept a donation towards our honeymoon to make it that little bit more special. Our destination is still being decided, but we want to make it an adventure!</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </li>
      </ul>
    </div>
    <!-- End Hero Slider -->
  </div>

  <div class="accent-bg padding-tb20 cta-fw payment-success" style="background-color:#37BC9B;display:none;">
    <div class="container">
      <h4 style="margin-top:5px;">Thank you for your donation!</h4>
    </div>
  </div>

  <div class="accent-bg padding-tb20 cta-fw payment-error" style="background-color:#F23827;display:none;">
    <div class="container">
      <h4>There was an error with your donation! Try again or contact me here: <a href="mailto:mark.oliver.cameron@gmail.com" style="color:white;">mark.oliver.cameron@gmail.com</a> or <a href="phone:+41788207122" style="color:white;">078 820 71 22</a></h4>
    </div>
  </div>

  <div class="featured-links row">
    <a id="donate20" href="#" class="featured-link col-md-4 col-sm-4">
      <span>&nbsp;Good idea</span>
      <strong>Donate CHF 50</strong>
    </a>
    <a id="donate50" href="#" class="featured-link col-md-4 col-sm-4">
      <span>&nbsp;Great idea</span>
      <strong>Donate CHF 100</strong>
    </a>
    <a href="#" class="featured-link col-md-4 col-sm-4" data-toggle="modal" data-target="#DonateModal">
      <span>&nbsp;I want to make it special</span>
      <strong>Donate other amount</strong>
    </a>
  </div>

  <div class="hero-area">
    <!-- Start Hero Slider -->
    <div class="flexslider heroflex hero-slider" data-autoplay="yes" data-pagination="no" data-arrows="yes" data-style="fade" data-pause="yes">
      <ul class="slides">
        <li class="parallax" style="background-image:url(/images/photo-2.jpeg)">

        </li>
        <li class="parallax" style="background-image:url(/images/photo-3.jpg)">

        </li>
        <li class="parallax" style="background-image:url(/images/photo-4.jpeg)">

        </li>
      </ul>
    </div>
    <!-- End Hero Slider -->
  </div>

  <script src="https://checkout.stripe.com/checkout.js"></script>

@endsection

@section('js')

  <script>

   function clearNotifications() {
     $('.payment-error').hide();
     $('.payment-success').hide();
     $('.modal-body').hide();
   }

   var donation_amount = 0;

   var handler = StripeCheckout.configure({
     key: '{{ config('services.stripe.key') }}',
     image: '/images/stripe-square.jpeg',
     locale: 'auto',
     token: function(token) {
       console.log(donation_amount);

       if (donation_amount == 0) {
         $('.payment-error').slideDown();
         return;
       }

       $('#overlay').show();

       $.ajax({
         type: 'POST',
         url: '/pay-stripe',
         data: {
           email: token.email,
           stripeToken: token.id,
           amount: donation_amount,
         },
         headers: {
           'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         },
         success: function (data) {
           $('#DonateModal').modal('hide');
           $('#overlay').hide();
           $('input[name="custom_amount"]').val('');
           $('input[name="donation-amount"]').each(function() {
             console.log("each?");
             $(this).prop('checked', false);
             $(this).parent().removeClass('selected');
           });
           $('.payment-success').slideDown();
         },
         error: function (data) {
           $('.payment-error').slideDown();
         },
       });
     }
   });

   document.getElementById('donate20').addEventListener('click', function(e) {
     donation_amount = 5000;

     clearNotifications();

     handler.open({
       name: 'Donate CHF 50',
       description: 'towards the honeymoon',
       currency: 'chf',
       amount: donation_amount
     });
     e.preventDefault();
   });

   document.getElementById('donate50').addEventListener('click', function(e) {
     donation_amount = 10000;

     clearNotifications();

     handler.open({
       name: 'Donate CHF 100',
       description: 'towards the honeymoon',
       currency: 'chf',
       amount: donation_amount
     });
     e.preventDefault();
   });

   document.getElementById('donate-other').addEventListener('click', function(e) {
     clearNotifications();

     var amount = $('input[name="custom_amount"]').val();

     if (amount == '') {
       amount = $('input[name="donation-amount"]:checked').val();
     }

     if (amount === '' || amount === undefined) {
       $('.modal-body').show();
       e.preventDefault();
       return;
     }

     donation_amount = amount * 100;

     handler.open({
       name: 'Donate CHF '+ amount,
       description: 'to Rachel and Mark\'s honeymoon',
       currency: 'chf',
       amount: amount * 100,
     });

     e.preventDefault();
   });

   // Close Checkout on page navigation:
   window.addEventListener('popstate', function() {
     handler.close();
   });

   $(document).ready(function() {

     $("#DonateModal").on('show.bs.modal', function(e) {
       $('.modal-body').hide();
     });

   });
  </script>

@endsection
