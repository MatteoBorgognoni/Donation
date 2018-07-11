<?php

namespace Drupal\donation\Plugin\Field\FieldType;

use Drupal\Component\Utility\Random;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'Donation customer address' field type.
 *
 * @FieldType(
 *   id = "generic_address",
 *   label = @Translation("Generic address"),
 *   description = @Translation("Field for storing generic address"),
 *   default_widget = "generic_address",
 *   default_formatter = "generic_address"
 * )
 */
class GenericAddress extends FieldItemBase {
  

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties = [];

    $properties['address_line_1'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Address line 1'));

    $properties['address_line_2'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Address line 2'));

    $properties['address_line_3'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Address line 3'));

    $properties['city'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('City'));

    $properties['post_code'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Postcode'));

    $properties['country'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Country'));

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    
    $schema = [
      'columns' => [
        'address_line_1' => [
          'type' => 'varchar',
          'length' => 64,
        ],
        'address_line_2' => [
          'type' => 'varchar',
          'length' => 64,
        ],
        'address_line_3' => [
          'type' => 'varchar',
          'length' => 64,
        ],
        'city' => [
          'type' => 'varchar',
          'length' => 64,
        ],
        'post_code' => [
          'type' => 'varchar',
          'length' => 12,
        ],
        'country' => [
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
  public static function generateSampleValue(FieldDefinitionInterface $field_definition) {

    $values['address_line_1'] = '3 Churchil road';
    $values['address_line_2'] = 'Hamilton house';
    $values['address_line_3'] = 'Flat 4';
    $values['city'] = 'Bristol';
    $values['post_code'] = 'BS5 0JQ';
    $values['country'] = 'UK';

    return $values;
  }



  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $data = [
      'address_line_1' => trim($this->get('address_line_1')->getValue()),
      'address_line_2' => trim($this->get('address_line_2')->getValue()),
      'address_line_3' => trim($this->get('address_line_3')->getValue()),
      'city' => trim($this->get('city')->getValue()),
      'post_code' => trim($this->get('post_code')->getValue()),
      'country' => trim($this->get('country')->getValue()),
    ];

    return implode('', $data) === '';
  }

}
