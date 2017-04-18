<?php

namespace Drupal\donation\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a Donation method item annotation object.
 *
 * @see \Drupal\donation\Plugin\DonationMethodManager
 * @see plugin_api
 *
 * @Annotation
 */
class DonationMethod extends Plugin {


  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The label of the plugin.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $label;

  
  /**
   * The Name of the configuration Settings
   *
   * @var string
   */
  public $config_name;

}
