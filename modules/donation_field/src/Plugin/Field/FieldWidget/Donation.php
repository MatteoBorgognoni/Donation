<?php

namespace Drupal\donation_field\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\donation\DonationManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'donation field' widget.
 *
 * Type - the field creator will select the donation profile to assign to this
 * field and the Content type to redirect to Widget - This field let the
 * content creator choose a final page (entity reference) for the donation when
 * creating a page Formatter - It will display to the final user a donation
 * amount form which will lead the user to the DonationPage.
 *
 * @FieldWidget(
 *   id = "donation_field",
 *   label = @Translation("Donation field"),
 *   field_types = {
 *     "donation_field"
 *   }
 * )
 */
class Donation extends WidgetBase implements ContainerFactoryPluginInterface {
  
  /** @var \Drupal\donation\DonationManager  */
  protected $donationManager;
  
  /**
   * {@inheritdoc}
   */
  public function __construct(
    $plugin_id,
    $plugin_definition,
    FieldDefinitionInterface $field_definition,
    array $settings,
    array $third_party_settings,
    DonationManager $donation_manager
  ) {
    parent::__construct(
      $plugin_id,
      $plugin_definition,
      $field_definition,
      $settings,
      $third_party_settings);
    $this->donationManager = $donation_manager;
    
  }
  
  /**
   * {@inheritdoc}
   */
  public static function create(
    ContainerInterface $container,
    array $configuration,
    $plugin_id,
    $plugin_definition
  ) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['third_party_settings'],
      $container->get('donation.manager')
    );
  }
  
  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $values = !is_null($items[$delta]) ? $items[$delta]->getValue() : [];
    
    $donation_types = $this->donationManager->getDonationTypes();
    
    $element['donation_type'] = [
      '#type' => 'select',
      '#title' => 'Donation Type',
      '#required' => TRUE,
      '#empty_option' => 'Select a Donation type',
      '#options' => $donation_types,
      '#default_value' =>  isset($values['donation_type']) ? $values['donation_type'] : NULL,
    ];
    
    $thanks_pages = $this->donationManager->getThanksPages();
    
    $element['thanks_id'] = [
      '#type' => 'select',
      '#title' => 'Thank you page',
      '#required' => FALSE,
      '#empty_option' => 'Select a Thank you page',
      '#options' => $thanks_pages,
      '#default_value' =>  isset($values['thanks_id']) ? $values['thanks_id'] : NULL,
      '#element_validate' => array(
        array($this, 'validateThanksId'),
      ),
    ];
    
    return $element;
  }
  
  /**
   * Validate the color text field.
   */
  public function validateThanksId($element, FormStateInterface $form_state) {
    $value = $element['#value'];
    $form_id = $form_state->getFormObject()->getFormId();
    if($form_id !== 'field_config_edit_form' && empty($value)) {
      $form_state->setError($element, 'Please select a Thank you page');
    }
  }
  
}
