<?php

namespace Drupal\donation_field\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldWidget\EntityReferenceAutocompleteWidget;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'donation' widget.
 *
 * @FieldWidget(
 *   id = "donation",
 *   label = @Translation("Donation"),
 *   field_types = {
 *     "donation"
 *   }
 * )
 */
class Donation extends EntityReferenceAutocompleteWidget {
  
  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $entity = $items->getEntity();
    $referenced_entities = $items->referencedEntities();
    
    // Append the match operation to the selection settings.
    $selection_settings = $this->getFieldSetting('handler_settings') + ['match_operator' => $this->getSetting('match_operator')];
    $element['#title'] = '"Thank you" page';
    $element['#description'] = 'Please select a page to redirect after the donation';
    
    $element += [
      '#type' => 'entity_autocomplete',
      '#description' => '"Thank you" Page',
      '#target_type' => $this->getFieldSetting('target_type'),
      '#selection_handler' => $this->getFieldSetting('handler'),
      '#selection_settings' => $selection_settings,
      // Entity reference field items are handling validation themselves via
      // the 'ValidReference' constraint.
      '#validate_reference' => FALSE,
      '#maxlength' => 1024,
      '#default_value' => isset($referenced_entities[$delta]) ? $referenced_entities[$delta] : NULL,
      '#size' => $this->getSetting('size'),
      '#placeholder' => $this->getSetting('placeholder'),
    ];
    if ($this->getSelectionHandlerSetting('auto_create') && ($bundle = $this->getAutocreateBundle())) {
      $element['#autocreate'] = [
        'bundle' => $bundle,
        'uid' => ($entity instanceof EntityOwnerInterface) ? $entity->getOwnerId() : \Drupal::currentUser()->id()
      ];
    }
    
    return ['target_id' => $element];
  }
  
}
