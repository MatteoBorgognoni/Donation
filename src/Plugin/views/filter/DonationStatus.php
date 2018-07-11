<?php

namespace Drupal\donation\Plugin\views\filter;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\filter\BooleanOperator;

/**
 * Filter to handle boolean status.
 *
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("donation_status")
 */
class DonationStatus extends BooleanOperator {
  
  /**
   * Return the possible options for this filter.
   *
   * Child classes should override this function to set the possible values
   * for the filter.  Since this is a boolean filter, the array should have
   * two possible keys: 1 for "True" and 0 for "False", although the labels
   * can be whatever makes sense for the filter.  These values are used for
   * configuring the filter, when the filter is exposed, and in the admin
   * summary of the filter.  Normally, this should be static data, but if it's
   * dynamic for some reason, child classes should use a guard to reduce
   * database hits as much as possible.
   */
  public function getValueOptions() {
    // Provide a fallback if the above didn't set anything.
    if (!isset($this->valueOptions)) {
      $this->valueOptions = [1 => $this->t('Completed'), 0 => $this->t('Abandoned')];
    }
  }
  
  protected function valueForm(&$form, FormStateInterface $form_state) {
    if (empty($this->valueOptions)) {
      // Initialize the array of possible values for this filter.
      $this->getValueOptions();
    }
    if ($exposed = $form_state->get('exposed')) {
      // Exposed filter: use a select box to save space.
      $filter_form_type = 'select';
    }
    else {
      // Configuring a filter: use radios for clarity.
      $filter_form_type = 'radios';
    }
  
    $value = $this->value;
    // Unless 'All' is provided as value, we cast the user input to int to have
    // a reliable boolean representation.
    if ($value !== 'All') {
      $value = (int) $value;
    }
    
    $form['value'] = [
      '#type' => $filter_form_type,
      '#title' => $this->value_value,
      '#options' => $this->valueOptions,
      '#default_value' => $value,
    ];
    if (!empty($this->options['exposed'])) {
      $identifier = $this->options['expose']['identifier'];
      $user_input = $form_state->getUserInput();
      if ($exposed && !isset($user_input[$identifier])) {
        $user_input[$identifier] = $this->value;
        $form_state->setUserInput($user_input);
      }
      // If we're configuring an exposed filter, add an - Any - option.
      if (!$exposed || empty($this->options['expose']['required'])) {
        $form['value']['#options'] = ['All' => $this->t('- Any -')] + $form['value']['#options'];
      }
    }
  }
  
  protected function defineOptions() {
    $options = parent::defineOptions();
    
    $options['value']['default'] = 'All';
    
    return $options;
  }
  
  /**
   * Make some translations to a form item to make it more suitable to
   * exposing.
   */
  protected function exposedTranslate(&$form, $type) {
    if (!isset($form['#type'])) {
      return;
    }
    
    if ($form['#type'] == 'radios') {
      $form['#type'] = 'select';
    }
    // Checkboxes don't work so well in exposed forms due to GET conversions.
    if ($form['#type'] == 'checkboxes') {
      if (empty($form['#no_convert']) || empty($this->options['expose']['multiple'])) {
        $form['#type'] = 'select';
      }
      if (!empty($this->options['expose']['multiple'])) {
        $form['#multiple'] = TRUE;
      }
    }
    if (empty($this->options['expose']['multiple']) && isset($form['#multiple'])) {
      unset($form['#multiple']);
      $form['#size'] = NULL;
    }
    
    // Cleanup in case the translated element's (radios or checkboxes) display value contains html.
    if ($form['#type'] == 'select') {
      $this->prepareFilterSelectOptions($form['#options']);
    }
    
    if ($type == 'value' && empty($this->always_required) && empty($this->options['expose']['required']) && $form['#type'] == 'select' && empty($form['#multiple'])) {
      
      $form['#options'] = ['All' => $this->t('- Any -')] + $form['#options'];
        if (!isset($this->value)) {
          $form['#default_value'] = 'All';
        }
        
    }
    
    if (!empty($this->options['expose']['required'])) {
      $form['#required'] = TRUE;
    }
  }
  
  
}
