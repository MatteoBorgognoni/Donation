<?php

namespace Drupal\donation\Plugin\views\field;

use Drupal\Core\Form\FormStateInterface;
use Drupal\donation\Entity\Donation;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * Field handler to display donation formatted Amount.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("donation_gift_aid_date")
 */
class DonationGiftAidDate extends FieldPluginBase {
  
  /**
   * @{inheritdoc}
   */
  public function query() {
    // Leave empty to avoid a query on this field.
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

    $has_gift_aid = $donation->get('gift_aid')->value;
    $date = '';
    if($has_gift_aid) {
      $date = date('d/m/Y H:i', $donation->getCreatedTime());
    }
    
    return $date;
  }
  
}