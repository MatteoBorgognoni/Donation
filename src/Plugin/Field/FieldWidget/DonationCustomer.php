<?php

namespace Drupal\donation\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'donation' widget.
 *
 * @FieldWidget(
 *   id = "donation_customer",
 *   label = @Translation("Donation Customer"),
 *   field_types = {
 *     "donation_customer"
 *   }
 * )
 */
class DonationCustomer extends WidgetBase {
  
  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
        'visible_elements' => [],
      ] + parent::defaultSettings();
  }
  
  
  public static function customerFields() {
    //TODO: move fields in YAML config page on order to be able to update fields
    
    $fields = [];

    $fields['title'] = [
      'title' => 'Title',
      'description' => '',
      'type' => 'select',
      'options' => [
        '#options' => [
          'Dr' => 'Dr',
          'Miss' => 'Miss',
          'Mr' => 'Mr',
          'Mrs' => 'Mrs',
          'Ms' => 'Ms',
          'Other' => 'Other',
        ],
        '#required' => TRUE,
        '#attributes' => [
          'class' => [
            'donation-customer',
            'title',
          ],
        ],
      ],
    ];
    
    $fields['first_name'] = [
      'title' => 'First name',
      'description' => '',
      'type' => 'textfield',
      'options' => [
        '#required' => TRUE,
        '#size' => 32,
        '#maxlength' => 64,
        '#attributes' => [
          'class' => [
            'donation-customer',
            'first-name',
          ],
        ],
        //'#placeholder' => $this->t('First Name'),
      ],
    ];
  
    $fields['last_name'] = [
      'title' => 'Last name',
      'description' => '',
      'type' => 'textfield',
      'options' => [
        '#required' => TRUE,
        '#size' => 32,
        '#maxlength' => 64,
        '#attributes' => [
          'class' => [
            'donation-customer',
            'last-name',
          ],
        ],
        //'#placeholder' => $this->t('Last Name'),
      ],
    ];

    $fields['email'] = [
      'title' => 'Email',
      'description' => '',
      'type' => 'email',
      'options' => [
        '#size' => 32,
        '#required' => TRUE,
        '#attributes' => [
          'class' => [
            'donation-customer',
            'email',
          ],
        ],
        //'#placeholder' => $this->t('Email'),
      ],
    ];

    $fields['phone_number'] = [
      'title' => 'Telephone number',
      'description' => '',
      'type' => 'tel',
      'options' => [
        '#required' => FALSE,
        '#attributes' => [
          'class' => [
            'donation-customer',
            'telephone-number',
          ],
        ],
        //'#placeholder' => $this->t('First Name'),
      ],
    ];
    
    return $fields;
  }
  
  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    
    $available_elements = [];
    
    foreach ($this->customerFields() as $key => $field) {
      $available_elements[$key] = $field['title'];
    }
    
    $element['visible_elements'] = [
      '#type' => 'checkboxes',
      '#title' => t('Visible Customer Elements'),
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
        $elements[] = $this->customerFields()[$element]['title'];
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
    
    foreach ($this->customerFields() as $key => $field) {
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
