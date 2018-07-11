<?php

namespace Drupal\donation\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\donation\Plugin\Field\FieldWidget\GenericAddress as Widget;

/**
 * Plugin implementation of the 'Generic Address Formatted' formatter.
 *
 * @FieldFormatter(
 *   id = "generic_address_formatted",
 *   label = @Translation("Generic Address Formatted"),
 *   field_types = {
 *     "generic_address"
 *   }
 * )
 */
class GenericAddressFormatted extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      // Implement default settings.
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    return [
      // Implement settings form.
    ] + parent::settingsForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    // Implement settings summary.

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    
    foreach ($items as $delta => $item) {
      $elements[$delta] = ['#markup' => $this->viewValue($item)];
    }

    return $elements;
  }

  /**
   * Generate the output appropriate for one field item.
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   One field item.
   *
   * @return string
   *   The textual output generated.
   */
  protected function viewValue(FieldItemInterface $item) {
    
    // Get fields definitions
    $addressfields = Widget::addressFields();
    
    $values = $item->getValue();
    
    // TODO better default field format
    foreach ($values as $key => $value) {
      if (is_string($value) && !empty($value) && isset($addressfields[$key])) {
        $output = '<div class="field__item field__' . Html::cleanCssIdentifier($key) . '>' . $value . '</div>';
        $fields[$key] = $output;
      }
    }

    return implode('', $fields);
  }

}
