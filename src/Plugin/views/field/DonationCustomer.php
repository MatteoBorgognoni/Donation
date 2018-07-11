<?php

namespace Drupal\donation\Plugin\views\field;

use Drupal\Core\Form\FormStateInterface;
use Drupal\donation\Entity\Donation;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * Field handler to display Customer data.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("donation_customer")
 */
class DonationCustomer extends FieldPluginBase {
  
  /**
   * @{inheritdoc}
   */
  public function query() {
    // Leave empty to avoid a query on this field.
  }
  
  /**
   * Define the available options
   * @return array
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
  
    $options['visible_elements'] = ['default' => []];
    
    return $options;
  }
  
  /**
   * Default options form that provides the label widget that all fields
   * should have.
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    

    
    $form['visible_elements'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Visible Elements'),
      '#options' => [
        'getFirstName' => 'First Name',
        'getLastName' => 'Last Name',
        'getFullName' => 'Full Name',
        'getEmail' => 'Email',
        'getCity' => 'City',
        'getPostCode' => 'Post Code',
        'getFormattedAddress' => 'Full Address'
      ],
      '#default_value' => $this->options['visible_elements'],
    ];
  

    
    parent::buildOptionsForm($form, $form_state);
  
  }
  
  /**
   * @{inheritdoc}
   */
  public function render(ResultRow $values) {
    $donation = $values->_entity;
    
    $visible_elements_options = $this->options['visible_elements'];
    $visible_elements = [];
    
    foreach ($visible_elements_options as $method => $is_visible) {
      if ($is_visible) {
        if($method == 'getFullName') {
          $visible_elements[] = $donation->{$method}(TRUE);
        } else {
          $visible_elements[] = $donation->{$method}();
        }
      }
    }
    
    $markup = '';
    if (!empty($visible_elements)) {
      $markup = implode('<br>', $visible_elements);
    }
    
    return $this->t($markup);
  }
  
}