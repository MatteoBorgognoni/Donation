<?php

namespace Drupal\donation\Plugin\DonationMethod;

use Drupal\donation\Plugin\DonationMethodBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\donation\Entity\Donation;

/**
 * @DonationMethod(
 *  id = "basic",
 *  label = @Translation("Basic Method"),
 *  config_name = "",
 *  transaction_map_field = "",
 *  customer_map_field = "",
 * )
 */
class Basic extends DonationMethodBase {
  /**
   * {@inheritdoc}
   */
  public function getSettings($name = '') {}
  
  
  /**
   * {@inheritdoc}
   */
  public function appendForm(Donation &$donation, array &$form, FormStateInterface &$form_state) {
    $elements['message'] = [
      '#type' => 'markup',
      '#markup' => t('<div>This message is a "Basic" placeholder payment method for thew donation entity.</div>'),
    ];
    
    return $elements;
  }
  
  /**
   * {@inheritdoc}
   */
  public function submitHandler(Donation &$donation, array &$form, FormStateInterface &$form_state) {
    drupal_set_message('Basic Payment Method submission');
  }
  
  /**
   * {@inheritdoc}
   */
  public function execute(Donation &$donation, array &$form, FormStateInterface &$form_state) {
    $donation->set('status', 1);
  }
  
  
}
