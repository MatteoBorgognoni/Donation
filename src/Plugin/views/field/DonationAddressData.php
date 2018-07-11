<?php

namespace Drupal\donation\Plugin\views\field;

use Drupal\Core\Form\FormStateInterface;
use Drupal\donation\Entity\Donation;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * Field handler to display the address elements of the donor.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("donation_address_data")
 */
class DonationAddressData extends FieldPluginBase {

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
        case 'address_line_1':
          return $donation->getAddressLine(1);
          break;
        case 'address_line_2':
          return $donation->getAddressLine(2);
          break;
        case 'address_line_3':
          return $donation->getAddressLine(3);
          break;
        case 'city':
          return $donation->getCity();
          break;
        case 'post_code':
          return $donation->getPostCode();
          break;
        case 'country':
          return $donation->getCountry();
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