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
  public function getSettings($name = '') {}
  
  /**
   * {@inheritdoc}
   */
  public function appendForm() {}
  
  
  
  /**
   * {@inheritdoc}
   */
  public function execute(Donation &$donation, array $form, FormStateInterface $form_state) {}
  
  
}
