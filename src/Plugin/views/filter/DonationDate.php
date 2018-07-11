<?php

namespace Drupal\donation\Plugin\views\filter;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\filter\Date;
use Drupal\Core\Datetime\DrupalDateTime;
use DateTime;

/**
 * Filter to handle dates stored as a timestamp.
 *
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("donation_date")
 */
class DonationDate extends Date {

  /**
   * Override parent method to change input type.
   */
  public function buildExposedForm(&$form, FormStateInterface $form_state) {
    parent::buildExposedForm($form, $form_state);
    
    // Change the form element to a 'datetime' if the exposed field is
    // configured for 'date' input.
    if ($this->value['type'] === 'date') {
      $field_identifier = $this->options['expose']['identifier'];
      
      if ($this->operator === 'between') {
        $form[$field_identifier]['min']['#type'] = 'date';
        $form[$field_identifier]['min']['#title'] = $this->t('From');
        $form[$field_identifier]['max']['#type'] = 'date';
        $form[$field_identifier]['max']['#title'] = $this->t('To');
        // Check the element input matches the form structure.
        $input = $form_state->getUserInput();
        if (isset($input[$field_identifier], $input[$field_identifier]['min']) &&  !is_array($input[$field_identifier]['min']) && $value = $this->value['min']) {
          $date = new DrupalDateTime($value);
          $input[$field_identifier]['min'] = [
            'date' => $date->format('Y-m-d'),
          ];
        }
        
        if (isset($input[$field_identifier], $input[$field_identifier]['max']) &&  !is_array($input[$field_identifier]['max']) && $value = $this->value['max']) {
          $date = new DrupalDateTime($value);
          $input[$field_identifier]['max'] = [
            'date' => $date->format('Y-m-d'),
          ];
        }
        
        $form_state->setUserInput($input);
      }
      else {
        $form[$field_identifier]['#type'] = 'date';
        
        // Check the element input matches the form structure.
        $input = $form_state->getUserInput();
        if (isset($input[$field_identifier]) &&  !is_array($input[$field_identifier]) && $value = $this->value['value']) {
          $date = new DrupalDateTime($value);
          $input[$field_identifier] = [
            'date' => $date->format('Y-m-d'),
          ];
        }
        $form_state->setUserInput($input);
      }
    }
  }
  
  
  protected function opBetween($field) {
    
    $a = intval(strtotime($this->value['min'], 0));
    $to_date = new DateTime($this->value['max']);
    $to_date->modify('+1 day');
    $b = intval(strtotime($to_date->format('Y-m-d'), 0));
    
    if ($this->value['type'] == 'offset') {
      $a = '***CURRENT_TIME***' . sprintf('%+d', $a); // keep sign
      $b = '***CURRENT_TIME***' . sprintf('%+d', $b); // keep sign
    }
    // This is safe because we are manually scrubbing the values.
    // It is necessary to do it this way because $a and $b are formulas when using an offset.
    $operator = strtoupper($this->operator);
    $this->query->addWhereExpression($this->options['group'], "$field $operator $a AND $b");
  }
  
}
