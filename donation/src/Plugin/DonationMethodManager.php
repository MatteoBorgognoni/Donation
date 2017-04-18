<?php

namespace Drupal\donation\Plugin;

use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;

/**
 * Provides the Donation method plugin manager.
 */
class DonationMethodManager extends DefaultPluginManager {


  /**
   * Constructor for DonationMethodManager objects.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/DonationMethod', $namespaces, $module_handler, 'Drupal\donation\Plugin\DonationMethodInterface', 'Drupal\donation\Annotation\DonationMethod');

    $this->alterInfo('donation_method_info');
    $this->setCacheBackend($cache_backend, 'donation_method_plugins');
  }

}
