<?php

/**
 * @file
 * Definition of Drupal\d8views\Plugin\views\field\NodeTypeFlagger
 */

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
 * @ViewsField("donation_response")
 */
class DonationResponse extends FieldPluginBase {
  
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
        'getTransactionMapField' => 'Transaction ID',
        'getCustomerMapField' => 'Customer ID',
      ],
      '#default_value' => $this->options['visible_elements'],
    ];

    parent::buildOptionsForm($form, $form_state);
  
  }
  
  /**
   * @{inheritdoc}
   */
  public function render(ResultRow $values) {
    /** @var Donation $donation */
    $donation = $this->getEntity($values);

    if(is_null($donation)) {
      return '';
    }

    $plugin = $donation->getMethodPlugin();
    $response = $donation->getResponseValues();

    $visible_elements_options = $this->options['visible_elements'];
    $visible_elements = [];

    foreach ($visible_elements_options as $method => $is_visible) {
      if ($is_visible) {
        $map_field = $plugin->{$method}();
        if(isset($response[$map_field])) {
          $visible_elements[] = $response[$map_field];
        }
      }
    }

    $markup = '';
    if(!empty($visible_elements)) {
      $markup .= implode('<br>', $visible_elements);
    }

    return $this->t($markup);
  }

}