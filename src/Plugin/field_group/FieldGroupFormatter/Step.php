<?php

namespace Drupal\donation\Plugin\field_group\FieldGroupFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Form\FormState;
use Drupal\Core\Template\Attribute;
use Drupal\field_group\FieldGroupFormatterBase;

/**
 * Plugin implementation of the 'step' formatter.
 * This plugin create just a container for fields without any additional markup -- no special behaviour
 *
 * @FieldGroupFormatter(
 *   id = "step",
 *   label = @Translation("Step"),
 *   description = @Translation("This fieldgroup renders the inner content in a Step element with classes and attributes."),
 *   supported_contexts = {
 *     "form",
 *   }
 * )
 */
class Step extends FieldGroupFormatterBase {
  
  /**
   * {@inheritdoc}
   */
  public function preRender(&$element, $rendering_object) {
    parent::preRender($element, $rendering_object);

  }
  
  /**
   * {@inheritdoc}
   */
  public function settingsForm() {
    $form = parent::settingsForm();

    if ($this->context == 'form') {
      $form['required_fields'] = array(
        '#type' => 'checkbox',
        '#title' => $this->t('Mark group as required if it contains required fields.'),
        '#default_value' => $this->getSetting('required_fields'),
        '#weight' => 2,
      );
    }
    
    return $form;
  }
  
  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    
    $summary = array();
    
    if ($this->getSetting('required_fields')) {
      $summary[] = $this->t('Mark as required');
    }
    
    return $summary;
  }
  
  /**
   * {@inheritdoc}
   */
  public static function defaultContextSettings($context) {
    $defaults = array(
        'required_fields' => $context == 'form',
      ) + parent::defaultSettings($context);
    
    if ($context == 'form') {
      $defaults['required_fields'] = 1;
    }
    
    return $defaults;
  }

}
