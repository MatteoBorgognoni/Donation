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
   * @param $name string.
   *   The Configuration name associated with the plugin.
   *
   * @return array().
   *   Render array of settings defined by the donation method
   */
  public function getSettings($name = '');
  
  
  /**
   * Return Form elements to append to the Donation Entity Form for this Donation Method.
   *
   */
  public function appendForm();
  
  /**
   * Execute Payment and add related information to the Donation object.
   *
   * @param $donation Donation.
   *   The Donation Entity being saved passed as reference.
   *
   */
  public function execute(Donation &$donation, array $form, FormStateInterface $form_state);

}
