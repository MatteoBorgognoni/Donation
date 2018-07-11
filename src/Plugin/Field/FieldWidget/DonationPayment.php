<?php

namespace Drupal\donation\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\donation\DonationManager;
use Drupal\donation\Plugin\DonationMethodManager;
use Drupal\Core\Field\FieldDefinitionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'donation_payment' widget.
 * This field widget will inject the form elements defined by the payment method modules
 *
 *
 * @FieldWidget(
 *   id = "donation_payment",
 *   label = @Translation("Donation Payment Form"),
 *   multiple_values = TRUE,
 *   field_types = {
 *     "donation_payment"
 *   }
 * )
 */
class DonationPayment extends WidgetBase implements ContainerFactoryPluginInterface {
  
  /**
   * The Donation payment method manager.
   *
   * @var \Drupal\donation\Plugin\DonationMethodManager
   */
  protected $donationMethodManager;
  
  /**
   * The Donation MAnager.
   *
   * @var \Drupal\donation\DonationManager
   */
  protected $donationManager;
  
  
  /**
   * Constructs a new instance.
   *
   * @param string $plugin_id
   *   The plugin_id for the widget.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The definition of the field to which the widget is associated.
   * @param mixed[] $settings
   *   The widget settings.
   * @param array[] $third_party_settings
   *   Any third party settings.
   * @param \Drupal\donation\Plugin\DonationMethodManager $donationMethodManager
   *   The payment line item manager.
   */
  public function __construct(
    $plugin_id, $plugin_definition,
    FieldDefinitionInterface $field_definition,
    array $settings, array
    $third_party_settings,
    DonationManager $donation_manager,
    DonationMethodManager $donationMethodManager
  ) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings);
    $this->donationManager = $donation_manager;
    $this->donationMethodManager = $donationMethodManager;
  }
  
  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['third_party_settings'],
      $container->get('donation.manager'),
      $container->get('plugin.manager.donation_method'));
  }


  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    
    /** @var \Drupal\donation\Entity\Donation $donation */
    $donation = $items->getEntity();
    
    // Get Donation bundle information
    $build_info = $form_state->getBuildInfo();
    $settings = $this->donationManager->getBundleSettings($donation->getType());
    
    // Get payment method ID
    $method_id = $settings->get('payment_method');
    
    // Create Plugin instance
    $instance = $this->donationMethodManager->createInstance($method_id);
    
    // Get form elements from plugin
    $element = $instance->appendform($donation, $form, $form_state);
    
    // Set method id as hidden field
    $element['value'] = [
        '#type' => 'hidden',
        '#default_value' => $method_id,
      ];
    return $element;
  }

}
