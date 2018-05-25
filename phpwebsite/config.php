<?php
require_once('vendor/autoload.php');

$stripe = array(
  "secret_key"      => "sk_test_ssZO7qK7MfG8qZZr3Z5yDGYE",
  "publishable_key" => "pk_test_kutGYmlEvWusMrY0i9dIXp05"
);

\Stripe\Stripe::setApiKey($stripe['secret_key']);
?>