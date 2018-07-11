<?php

namespace Drupal\donation\Plugin;

use Drupal\donation\Entity\Donation;
use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines an interface for Donation method plugins.
 */
interface DonationMethodInterface extends PluginInspectionInterface  {
  
  
  /**
   * Return the label of the Donation Method plugin.
   *
   * @return string
   */
  public function getLabel();
  

  
  /**
   * Get the payment method settings.
   *
   * @return array().
   *   Render array of settings defined by the donation method
   */
  public function getSettings();
  
  
  /**
   * Get the payment method transaction machine name.

   * @return string.
   *   Machine name for the vendor transaction id
   */
  public function getTransactionMapField();

  
  /**
   * Get the payment method transaction machine name.

   * @return string.
   *   Machine name for the vendor customer id
   */
  public function getCustomerMapField();
  
  /**
   * @param \Drupal\donation\Entity\Donation $donation
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *
   * @return array().
   *   Form elements to embed in the donation_payment field widget
   */
  public function appendForm(Donation &$donation, array &$form, FormStateInterface &$form_state);


  /**
   * @param \Drupal\donation\Entity\Donation $donation
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *
   * Execute submission logic if needed
   *
   */
  public function submitHandler(Donation &$donation, array &$form, FormStateInterface &$form_state);
  
  /**
   * Execute Payment and add related information to the Donation object.
   *
   * @param $donation Donation.
   *   The Donation Entity being saved passed as reference.
   *
   * @param $form array.
   *   The Form array passed as reference.
   *
   * @param \Drupal\Core\Form\FormStateInterface $form_state.
   *   The Form State object passed as reference.
   *
   * @return $data array().
   *   Render array containing Errors and the Response
   */
  public function execute(Donation &$donation, array &$form, FormStateInterface &$form_state);

}
