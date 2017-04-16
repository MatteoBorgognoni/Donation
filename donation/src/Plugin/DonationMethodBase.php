<?php

namespace Drupal\donation\Plugin;

use Drupal\donation\Entity\DonationInterface;
use Drupal\Component\Plugin\PluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
  public function execute(DonationInterface &$donation) {}
  
  
}
