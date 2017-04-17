<?php

namespace Drupal\donation_stripe\Plugin\DonationMethod;

use Drupal\donation\Plugin\DonationMethodBase;
use Drupal\donation\Entity\DonationInterface;
use Drupal\Core\Form\FormStateInterface;
use Stripe\Stripe as StripeApi;
use Stripe\Charge;
use Stripe\Error;

/**
 * @DonationMethod(
 *  id = "stripe",
 *  label = @Translation("Stripe"),
 *  config_name = "",
 * )
 */
class Stripe extends DonationMethodBase {
  
  /**
   * {@inheritdoc}
   */
  public function appendForm() {
    $elements = [];
  
    $config = \Drupal::config('donation_stripe.settings');
    $mode = $config->get('mode');
    
    $key = $config->get($mode . '_publishable_key');
    $elements['#attached']['drupalSettings']['donation_stripe']['key'] = $key;
    
    $elements['#attached']['library'] = ['donation_stripe/donation_stripe_js'];
    
    $elements['card_number'] = array(
      '#title' => t('Card Number'),
      '#type' => 'textfield',
      '#size' => 20,
      '#maxlength' => 20,
      '#attributes' => ['autocomplete' => 'off', 'name' => '', 'class' => ['card-number']],
  
    );
  
    $months = range(1, 12);
    $month_options = [];
    foreach ($months as $month) {
      $month_options[$month] = $month;
    }
    $elements['expiration_month'] = array(
      '#title' => t('Month'),
      '#type' => 'select',
      '#options' => $month_options,
      '#attributes' => ['autocomplete' => 'off', 'name' => '', 'class' => ['card-expiry-month']],
    );
    
    
    $years = range(date('Y'),date('Y') + 20);
    $year_options = [];
    foreach ($years as $year) {
      $year_options[$year] = $year;
    }
    $elements['expiration_year'] = array(
      '#title' => t('Year'),
      '#type' => 'select',
      '#options' => $year_options,
      '#attributes' => ['autocomplete' => 'off', 'name' => '', 'class' => ['card-expiry-year']],
    );
  
  
    $elements['cvc'] = array(
      '#title' => t('CVC'),
      '#type' => 'textfield',
      '#size' => 4,
      '#maxlength' => 4,
      '#attributes' => ['autocomplete' => 'off', 'name' => '', 'class' => ['card-cvc']],
    );
  
    $elements['stripeToken'] = array(
      '#type' => 'hidden',
      '#name' => 'stripeToken',
      '#value' => '',
      '#attributes' => ['class' => ['stripe-token']],
    );
  
    $elements['errors'] = array(
      '#type' => 'markup',
      '#markup' => '<div id="payment-errors"></div>'
    );



    return $elements;
  }
  
  /**
   * {@inheritdoc}
   */
  public function execute(DonationInterface &$donation, array $form, FormStateInterface $form_state) {
  
    $config = \Drupal::config('donation_stripe.settings');
    $mode = $config->get('mode');
  
    $key = $config->get($mode . '_secret_key');
  
    $token = $form_state->getUserInput()['stripeToken'];
  
    StripeApi::setApiKey($key);
  
    $amount = $donation->getAmount();
    $currency = $donation->getCurrencyCode();
  
    try {
      $charge = Charge::create(array(
        'amount' => $amount, // Amount in cents!
        'currency' => $currency,
        'source' => $token,
      ));
      ksm($charge);
    } catch (Error\Card $e) {
      //TODO:  handling errors
      $charge = FALSE;
    }
    
    return $charge;
  
  }
  
  
}
