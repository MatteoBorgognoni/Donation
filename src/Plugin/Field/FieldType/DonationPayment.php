<?php

namespace Drupal\donation\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'donation_payment' field type.
 *
 * @FieldType(
 *   id = "donation_payment",
 *   label = @Translation("Donation Payment Form"),
 *   description = @Translation("Payment Form field type for the Donation module"),
 *   default_widget = "donation_payment",
 *   default_formatter = "donation_payment",
 * )
 */
class DonationPayment extends FieldItemBase {
  
  
  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    // Prevent early t() calls by using the TranslatableMarkup.
    $properties['value'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Payment Method'))
      ->setRequired(TRUE);

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    $schema = [
      'columns' => [
        'value' => [
          'type' => 'varchar',
          'length' => 32,
        ],
      ],
    ];

    return $schema;
  }
  
  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $value = $this->get('value')->getValue();
    return $value === NULL || $value === '';
  }
  
  
}
