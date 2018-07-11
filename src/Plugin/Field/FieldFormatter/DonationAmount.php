<?php

namespace Drupal\donation\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\Plugin\Field\FieldFormatter\StringFormatter;
use Drupal\Core\Field\FieldItemListInterface;

/**
 * Plugin implementation of the 'donation_amount' formatter.
 *
 * @FieldFormatter(
 *   id = "donation_amount",
 *   label = @Translation("Donation amount"),
 *   field_types = {
 *     "donation_amount"
 *   }
 * )
 */
class DonationAmount extends StringFormatter {

  
  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    /** @var \Drupal\donation\Entity\Donation $donation */
    $donation = $items->getEntity();

    foreach ($items as $delta => $item) {
      $view_value = (int) $item->value;
      $elements[$delta] = [
        '#markup' => $donation->getFormattedAmount(),
      ];
      

    }
    return $elements;
  }
  

}
