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
 * Plugin implementation of the 'Donation customer' field type.
 *
 * @FieldType(
 *   id = "donation_customer",
 *   label = @Translation("Donation customer"),
 *   description = @Translation("Field for storing donor info"),
 *   default_widget = "donation_customer",
 *   default_formatter = "donation_customer"
 * )
 */
class DonationCustomer extends FieldItemBase {
  

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties = [];

    $properties['title'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Title'));

    $properties['first_name'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('First name'));
    
    $properties['last_name'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Last name'));

    $properties['email'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Email'));

    $properties['phone_number'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Phone number'));

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    
    $schema = [
      'columns' => [
        'title' => [
          'type' => 'varchar',
          'length' => 16,
        ],
        'first_name' => [
          'type' => 'varchar',
          'length' => 32,
        ],
        'last_name' => [
          'type' => 'varchar',
          'length' => 32,
        ],
        'email' => [
          'type' => 'varchar',
          'length' => 32,
        ],
        'phone_number' => [
          'type' => 'varchar',
          'length' => 16,
        ],
      ],
    ];

    return $schema;
  }


  /**
   * {@inheritdoc}
   */
  public static function generateSampleValue(FieldDefinitionInterface $field_definition) {

    $values['title'] = 'Mr';
    $values['first_name'] = 'John';
    $values['last_name'] = 'John';
    $values['email'] = 'johnsmith@example.com';
    $values['phone_number'] = '01234567891';

    return $values;
  }



  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $data = [
      'title' => trim($this->get('title')->getValue()),
      'first_name' => trim($this->get('first_name')->getValue()),
      'last_name' => trim($this->get('last_name')->getValue()),
      'email' => trim($this->get('email')->getValue()),
      'phone_number' => trim($this->get('phone_number')->getValue()),
    ];

    return implode('', $data) === '';
  }

}
