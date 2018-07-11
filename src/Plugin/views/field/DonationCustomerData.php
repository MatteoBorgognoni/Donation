<?php

namespace Drupal\donation\Plugin\views\field;

use Drupal\Core\Form\FormStateInterface;
use Drupal\donation\Entity\Donation;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * Field handler to display the full name of the donor.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("donation_customer_data")
 */
class DonationCustomerData extends FieldPluginBase {
  
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
    /* @var \Drupal\donation\Entity\DonationInterface $donation */
    $donation = $this->getEntity($values);

    if(!is_null($donation)) {
      $field = $this->field;

      switch ($field) {
        case 'title':
          return $donation->getCustomerTitle();
          break;
        case 'first_name':
          return $donation->getFirstName();
          break;
        case 'last_name':
          return $donation->getLastName();
          break;
        case 'full_name':
          return $donation->getFullName();
          break;
        case 'email':
          return $donation->getEmail();
          break;
        case 'phone':
          return $donation->getPhone();
          break;
        default:
          return '';
      }
    }
    else {
      return '';
    }
    
  }
  
}