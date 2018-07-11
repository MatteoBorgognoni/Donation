<?php

namespace Drupal\donation\Plugin\views\field;

use Drupal\Core\Form\FormStateInterface;
use Drupal\donation\Entity\Donation;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * Field handler to display the email of the donor.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("donation_email")
 */
class DonationEmail extends FieldPluginBase {
  
  /**
   * @{inheritdoc}
   */
  public function query() {
    // Leave empty to avoid a query on this field.
  }
  
  /**
   * Define the available options
   * @return array
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    
    return $options;
  }
  
  /**
   * @{inheritdoc}
   */
  public function render(ResultRow $values) {
    /** @var Donation $donation */
    $donation = $this->getEntity($values);

    if(is_null($donation)) {
      return '';
    }

    return $this->t($donation->getEmail());
  }
  
}