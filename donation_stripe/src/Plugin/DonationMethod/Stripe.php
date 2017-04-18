<?php

namespace Drupal\donation_stripe\Plugin\DonationMethod;

use Drupal\donation\Plugin\DonationMethodBase;
use Drupal\donation\Entity\Donation;
use Drupal\Core\Form\FormStateInterface;
use Stripe\Stripe as StripeApi;
use Stripe\Charge;
use Stripe\Error;
use Stripe\Customer;

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
  public function execute(Donation &$donation, array $form, FormStateInterface $form_state) {
  
    $config = \Drupal::config('donation_stripe.settings');
    $mode = $config->get('mode');
    $key = $config->get($mode . '_secret_key');
  
    // Stores errors:
    $errors = [];
    $input = $form_state->getUserInput();
    
    // Need a payment token:
    if (isset($input['stripeToken'])) {
      $token = $input['stripeToken'];
    } else {
      $errors['token'] = 'The order cannot be processed. Please make sure you have JavaScript enabled and try again.';
    }
    
    $charge = FALSE;
    $amount = $donation->getAmount(TRUE);
    $email = $donation->getEmail(TRUE);
    $full_name = $donation->getFullName();
    $currency = $donation->getCurrencyCode();
    $stripe_info = $donation->getResponseByMail($email);
    
    // If no errors, process the order:
    if (empty($errors)) {
      
      try {
        StripeApi::setApiKey($key);
  
        if(!$stripe_info) {
          $customer = Customer::create([
            'description' => 'Customer for ' . $email,
            'source' => $token,
          ]);
          ksm('A');
        } else {
          $customer = Customer::retrieve($stripe_info['customer_id']);
        }
        
        $charge = Charge::create([
          'amount' => $amount, // Amount in cents!
          'currency' => $currency,
          'source' => $token,
          'description' => 'Donation from ' . $email,
          'customer' => $customer->id,
        ]);
  
        $values = [
          'charge_id' => $charge->id,
          'customer_id' => $charge->customer,
          'status' => $charge->status,
        ];
        
        // Check that it was paid:
        if ($charge->paid == true) {
          
          $donation->set('status', 1);
          $donation->set('response', $values);
    
        } else { // Charge was not paid!
  
          $donation->set('status', 2);
          $donation->set('response', $values);
          
        }
        
      } catch (Error\Card $e) {
        //TODO:  handling errors
        // Card was declined.
        $e_json = $e->getJsonBody();
        $err = $e_json['error'];
        $errors['stripe'] = $err['message'];
      } catch (Error\ApiConnection $e) {
        $errors['network'] = 'Network Error';
      } catch (Error\InvalidRequest $e) {
        $errors['request'] = 'Bad Request';
      } catch (Error\Api $e) {
        $errors['server'] = 'Stripe servers down';
      } catch (Error\Base $e) {
        $errors['other'] = 'Unknown error';
      }
    } else {
      $form_state->setError('form', $errors['token']);
    }
    
    $data = [
      'errors' => $errors,
      'response' => $charge,
    ];
    
    return $data;
  
  }
  
  
}
