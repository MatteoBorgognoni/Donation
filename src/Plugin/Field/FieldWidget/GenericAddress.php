<?php

namespace Drupal\donation\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'Generic Address' widget.
 *
 * @FieldWidget(
 *   id = "generic_address",
 *   label = @Translation("Generic Address"),
 *   field_types = {
 *     "generic_address"
 *   }
 * )
 */
class GenericAddress extends WidgetBase {
  
  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
        'visible_elements' => [],
      ] + parent::defaultSettings();
  }
  
  
  public static function addressFields() {
    //TODO: move fields in YAML config page on order to be able to update fields
    
    $fields = [];
    
    $fields['address_line_1'] = [
      'title' => 'Address line 1',
      'description' => '',
      'type' => 'textfield',
      'options' => [
        '#required' => TRUE,
        '#size' => 32,
        '#maxlength' => 64,
        '#attributes' => [
          'class' => [
            'donation-customer',
            'address',
            'address-line-1',
          ],
        ],
      ],
    ];
  
    $fields['address_line_2'] = [
      'title' => 'Address line 2',
      'description' => '',
      'type' => 'textfield',
      'options' => [
        '#size' => 32,
        '#maxlength' => 64,
        '#attributes' => [
          'class' => [
            'donation-customer',
            'address',
            'address-line-2',
          ],
        ],
      ],
    ];
  
    $fields['address_line_3'] = [
      'title' => 'Address line 3',
      'description' => '',
      'type' => 'textfield',
      'options' => [
        '#size' => 32,
        '#maxlength' => 64,
        '#attributes' => [
          'class' => [
            'donation-customer',
            'address',
            'address-line-3',
          ],
        ],
      ],
    ];
  
    $fields['city'] = [
      'title' => 'City',
      'description' => '',
      'type' => 'textfield',
      'options' => [
        '#required' => TRUE,
        '#size' => 32,
        '#maxlength' => 64,
        '#attributes' => [
          'class' => [
            'donation-customer',
            'address',
            'city',
          ],
        ],
      ],
    ];
    
    $fields['post_code'] = [
      'title' => 'Postcode',
      'description' => '',
      'type' => 'textfield',
      'options' => [
        '#required' => TRUE,
        '#size' => 9,
        '#maxlength' => 9,
        '#attributes' => [
          'class' => [
            'donation-customer',
            'address',
            'postcode',
          ],
        ],
      ],
    ];
  
    $fields['country'] = [
      'title' => 'Country',
      'description' => '',
      'type' => 'textfield',
      'options' => [
        '#required' => TRUE,
        '#size' => 32,
        '#maxlength' => 32,
        '#attributes' => [
          'class' => [
            'donation-customer',
            'address',
            'country',
          ],
        ],
      ],
    ];
    
    return $fields;
  }
  
  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    
    $available_elements = [];
    
    foreach ($this->addressFields() as $key => $field) {
      $available_elements[$key] = $field['title'];
    }
    
    $element['visible_elements'] = [
      '#type' => 'checkboxes',
      '#title' => t('Visible Address Elements'),
      '#default_value' => $this->getSetting('visible_elements'),
      '#options' => $available_elements,
    ];
    return $element;
  }
  
  
  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $settings = $this->getSetting('visible_elements');
    $elements = [];
    
    foreach ($settings as $element) {
      if ($element) {
        $elements[] = $this->addressFields()[$element]['title'];
      }
    }
    
    $summary = [];
    $summary[] = t('Enabled fields: @elements', ['@elements' => implode(', ', $elements)]);
    return $summary;
  }

  
  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $values = !is_null($items[$delta]) ? $items[$delta]->getValue() : [];
    $settings = $this->getSetting('visible_elements');

    foreach ($this->addressFields() as $key => $field) {
      if (in_array($key, $settings, TRUE)) {
        $element[$key] = $this->prepareField($field);
        $element[$key]['#default_value'] = isset($values[$key]) ? $values[$key] : NULL;
      }
    }
    
    return $element;
  }
  
  
  protected function prepareField($field) {
    $element = [
      '#title' => $field['title'],
      '#description' => $field['description'],
      '#type' => $field['type'],
    ];
    foreach ($field['options'] as $optionKey => $option) {
      $element[$optionKey] = $option;
    }
    return $element;
  }
  
  
}
