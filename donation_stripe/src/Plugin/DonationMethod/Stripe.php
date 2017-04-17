<?php

namespace Drupal\donation_stripe\Plugin\DonationMethod;

use Drupal\donation\Plugin\DonationMethodBase ;

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
  
    $elements['card_number'] = array(
      '#title' => t('Card Number'),
      '#type' => 'textfield',
      '#size' => 20,
      '#maxlength' => 20,
      '#attributes' => ['autocomplete' => 'off', 'name' => '', 'class' => ['card-number']],
  
    );
  
    $elements['cvc'] = array(
      '#title' => t('CVC'),
      '#type' => 'textfield',
      '#size' => 4,
      '#maxlength' => 4,
      '#attributes' => ['autocomplete' => 'off', 'name' => '', 'class' => ['card-cvc']],
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
  
}
