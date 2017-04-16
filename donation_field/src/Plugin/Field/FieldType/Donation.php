<?php

namespace Drupal\donation_field\Plugin\Field\FieldType;

use Drupal\Core\Field\Plugin\Field\FieldType\EntityReferenceItem;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'donation' field type.
 *
 * @FieldType(
 *   id = "donation",
 *   label = @Translation("Donation"),
 *   description = @Translation("Donation field type"),
 *   default_widget = "donation",
 *   default_formatter = "donation",
 *   list_class = "\Drupal\Core\Field\EntityReferenceFieldItemList"
 * )
 */
class Donation extends EntityReferenceItem {
  
  /**
   * {@inheritdoc}
   */
  public static function defaultFieldSettings() {
    return [
        'donation_profile' => '',
      ] + parent::defaultFieldSettings();
  }
  
  
  /**
   * {@inheritdoc}
   */
  public function fieldSettingsForm(array $form, FormStateInterface $form_state) {
    $form = parent::fieldSettingsForm($form, $form_state);

    $bundleManager = \Drupal::service('entity_type.bundle.info');
    $availables_profiles = $bundleManager->getBundleInfo('donation');
    $profiles = [];
    
    foreach ($availables_profiles as $profile_id => $profile) {
      $profiles[$profile_id] = $profile['label'];
    }
    
    
    $form['donation'] = [
      '#type' => 'details',
      '#title' => t('Donation Settings'),
      '#open' => TRUE,
      '#process' => [[get_class($this), 'formProcessMergeParent']],
    ];
    
    $form['donation']['donation_profile'] = [
      '#type' => 'select',
      '#title' => $this->t('Donation Profile'),
      '#options' => $profiles,
      '#default_value' => $this->getSetting('donation_profile'),
    ];
    return $form;
  }

  
}
