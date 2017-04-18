<?php

namespace Drupal\donation\Plugin\Field\FieldFormatter;


use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Url;

/**
 * Plugin implementation of the 'donation_customer' formatter.
 *
 * @FieldFormatter(
 *   id = "donation_referencer",
 *   label = @Translation("Donation Reference"),
 *   field_types = {
 *     "string"
 *   }
 * )
 */
class DonationReference extends FormatterBase {
  

  
  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    
    foreach ($items as $delta => $item) {
      $elements[$delta] = ['#markup' => $this->viewValue($item)];
    }
    
    return $elements;
  }
  
  /**
   * Generate the output appropriate for one field item.
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   One field item.
   *
   * @return string
   *   The textual output generated.
   */
  protected function viewValue(FieldItemInterface $item) {
    $reference_values = unserialize($item->getValue()['value']);
    $reference = \Drupal::entityTypeManager()->getStorage($reference_values['entity_type'])->load($reference_values['id']);
    $route_name = 'entity.' . $reference_values['entity_type'] . '.canonical';
    $entity_values = [$reference_values['entity_type'] => $reference->id()];

    return \Drupal::l(
      $reference->label(),
      new Url( $route_name, $entity_values )
    );
  }
  
}