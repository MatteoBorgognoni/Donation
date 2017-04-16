<?php

namespace Drupal\donation\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\donation\Plugin\DonationMethodManager;
use Drupal\Core\Field\FieldDefinitionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'donation_payment' widget.
 *
 * @FieldWidget(
 *   id = "donation_payment",
 *   label = @Translation("Donation Payment Form"),
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
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, array $third_party_settings, DonationMethodManager $donationMethodManager) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings);
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
      $container->get('plugin.manager.donation_method'));
  }
  
  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
        'payment_method' => '',
      ] + parent::defaultSettings();
  }
  
  
  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    
    $available_methods = $this->donationMethodManager->getDefinitions();
    
    $methods = [];
    foreach ($available_methods as $method) {
      $methods[$method['id']] = $method['label']->render();
    }
    
    $element['payment_method'] = [
      '#type' => 'select',
      '#title' => t('Payment Method'),
      '#default_value' => $this->getSetting('payment_method'),
      '#options' => $methods,
    ];
    return $element;
  }
  
  
  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];

    $summary[] = t('Payment Method: @method', ['@method' => $this->getSetting('payment_method')]);

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    
    $method_id = $this->getSetting('payment_method');
    $instance = $this->donationMethodManager->createInstance($method_id);

    $element = $instance->appendform();
    
    $element['value'] = [
        '#type' => 'hidden',
        '#default_value' => $method_id,
      ];
    
    return $element;
  }

}
