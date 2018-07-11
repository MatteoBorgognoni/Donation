<?php

namespace Drupal\donation\Plugin\views\field;

use Drupal\Core\Form\FormStateInterface;
use Drupal\donation\Entity\Donation;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * Field handler to display donation formatted Amount.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("donation_formatted_amount")
 */
class DonationFormattedAmount extends FieldPluginBase {
  
  /**
   * @{inheritdoc}
   */
  public function query() {
    // Leave empty to avoid a query on this field.
  }
  
  /**
   * {@inheritdoc}
   */
  public function clickSort($order) {
    if (isset($this->field_alias)) {
  
      $this->field_alias = 'amount';

    }
    // Since fields should always have themselves already added, just
    // add a sort on the field.
    $params = $this->options['group_type'] != 'group' ? ['function' => $this->options['group_type']] : [];
    $this->query->addOrderBy(NULL, NULL, $order, $this->field_alias, $params);
  }
  
  /**
   * Define the available options
   * @return array
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    
    return $options;
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
    return $donation->getFormattedAmount();
  }
  
}