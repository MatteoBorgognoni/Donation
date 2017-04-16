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
 *     "map"
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
    $fields = [];
    
    $fields['email'] = [
      'title' => 'Email',
      'description' => '',
      'type' => 'email',
      'options' => [
        '#size' => 32,
        //'#placeholder' => $this->t('Email'),
      ],
    ];
    
    $fields['first_name'] = [
      'title' => 'First Name',
      'description' => '',
      'type' => 'textfield',
      'options' => [
        '#size' => 32,
        '#maxlength' => 32,
        //'#placeholder' => $this->t('First Name'),
      ],
    ];
  
    $fields['last_name'] = [
      'title' => 'Last Name',
      'description' => '',
      'type' => 'textfield',
      'options' => [
        '#size' => 32,
        '#maxlength' => 32,
        //'#placeholder' => $this->t('Last Name'),
      ],
    ];
  
    $fields['address_1'] = [
      'title' => 'Address Line 1',
      'description' => '',
      'type' => 'textfield',
      'options' => [
        '#size' => 32,
        '#maxlength' => 32,
      ],
    ];
  
    $fields['address_2'] = [
      'title' => 'Address Line 2',
      'description' => '',
      'type' => 'textfield',
      'options' => [
        '#size' => 32,
        '#maxlength' => 32,
      ],
    ];
  
    $fields['address_3'] = [
      'title' => 'Address Line 3',
      'description' => '',
      'type' => 'textfield',
      'options' => [
        '#size' => 32,
        '#maxlength' => 32,
      ],
    ];
  
    $fields['city'] = [
      'title' => 'City',
      'description' => '',
      'type' => 'textfield',
      'options' => [
        '#size' => 32,
        '#maxlength' => 32,
      ],
    ];
    
    $fields['post_code'] = [
      'title' => 'Post Code',
      'description' => '',
      'type' => 'textfield',
      'options' => [
        '#size' => 9,
        '#maxlength' => 9,
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
    $values = $items[$delta]->getValue();
    $settings = $this->getSetting('visible_elements');
    ksm($settings);
    foreach ($this->customerFields() as $key => $field) {
      if (in_array($key, $settings, TRUE)) {
        $element[$key] = $this->prepareField($field);
        $element[$key]['#default_value'] = isset($values[$key]) ? $values[$key] : NULL;
      }
    }
  
    $element['user_id'] = [
      '#type' => 'hidden',
      '#value' => isset($values['user_id']) ? $values['user_id'] : 'not_set',
    ];
    
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
