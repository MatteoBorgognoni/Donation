<?php

namespace Drupal\donation_field\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'donation' field type.
 * Type - the field creator will select the donation profile to assign to this field and the Content type to redirect to
 * Widget - This field let the content creator choose a final page (entity reference) for the donation when creating a page
 * Formatter - It will display to the final user a donation amount form which will lead the user to the DonationPage.
 *
 * @FieldType(
 *   id = "donation_field",
 *   label = @Translation("Donation field"),
 *   description = @Translation("Donation field type"),
 *   default_widget = "donation_field",
 *   default_formatter = "donation_field",
 * )
 */
class Donation extends FieldItemBase {
  
  
  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties = [];
    
    $properties['donation_type'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Donation type'));
    
    $properties['thanks_id'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Thank you page'));
    
    return $properties;
  }
  
  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    
    $schema = [
      'columns' => [
        'donation_type' => [
          'type' => 'varchar',
          'length' => 64,
        ],
        'thanks_id' => [
          'type' => 'int',
          'size' => 'small',
        ],
      ],
    ];
    
    return $schema;
  }
  
  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    
    $donation_type = $this->get('donation_type')->getValue();
    $thanks_id = $this->get('thanks_id')->getValue();
    
    return (empty($donation_type) || is_null($donation_type)) && (empty($thanks_id) || is_null($thanks_id));
  }
  
}
