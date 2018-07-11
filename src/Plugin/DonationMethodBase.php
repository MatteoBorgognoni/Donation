<?php

namespace Drupal\donation\Plugin;

use Drupal\donation\Entity\Donation;
use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Base class for Donation method plugins.
 */
abstract class DonationMethodBase extends PluginBase implements DonationMethodInterface {
  
  /**
   * {@inheritdoc}
   */
  public function getLabel() {
    return $this->pluginDefinition['label'];
  }
  
  /**
   * {@inheritdoc}
   */
  public function getSettings() {
    $config_name = $this->getPluginDefinition()['config_name'];
    if ($config_name) {
      return \Drupal::config($config_name);
    }
    else {
      return FALSE;
    }
  }
  
  /**
   * {@inheritdoc}
   */
  public function getTransactionMapField() {
    $transaction_map_field = $this->getPluginDefinition()['transaction_map_field'];
    return $transaction_map_field;
  }
  
  /**
   * {@inheritdoc}
   */
  public function getCustomerMapField() {
    $customer_map_field = $this->getPluginDefinition()['customer_map_field'];
    return $customer_map_field;
  }
  
  /**
   * {@inheritdoc}
   */
  public function appendForm(Donation &$donation, array &$form, FormStateInterface &$form_state) {}
  
  /**
   * {@inheritdoc}
   */
  public function submitHandler(Donation &$donation, array &$form, FormStateInterface &$form_state) {}
  
  /**
   * {@inheritdoc}
   */
  public function execute(Donation &$donation, array &$form, FormStateInterface &$form_state) {}
  
  
}
