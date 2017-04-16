<?php

namespace Drupal\donation_field\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityFormBuilderInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Plugin implementation of the 'donation' formatter.
 *
 * @FieldFormatter(
 *   id = "donation",
 *   label = @Translation("Donation field formatter"),
 *   field_types = {
 *     "donation"
 *   }
 * )
 */
class Donation extends FormatterBase implements ContainerFactoryPluginInterface {
  
  /**
   * The entity form builder.
   *
   * @var \Drupal\Core\Entity\EntityFormBuilderInterface
   */
  protected $entityFormBuilder;
  
 
  /**
   * The payment storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $donationStorage;
  
  /**
   * The request stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;
  
  
  
  /**
   * Constructs a new instance.
   *
   * @param string $plugin_id
   *   The plugin_id for the formatter.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The definition of the field to which the formatter is associated.
   * @param array $settings
   *   The formatter settings.
   * @param string $label
   *   The formatter label display setting.
   * @param string $view_mode
   *   The view mode.
   * @param array $third_party_settings
   *   Any third party settings.
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings, RequestStack $request_stack, EntityFormBuilderInterface $entity_form_builder, EntityStorageInterface $donation_storage) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
    $this->entityFormBuilder = $entity_form_builder;
    $this->donationStorage = $donation_storage;
    $this->requestStack = $request_stack;
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
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('request_stack'),
      $container->get('entity.form_builder'),
      $container->get('entity.manager')->getStorage('donation')
    );
  }
  
  
  
  
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
    
    $form_settings = [];
    $form_settings['donation_profile'] = $this->fieldDefinition->getSetting('donation_profile');
    $form_settings['reference'] = $this->getCurrentPage();
    $form_settings['redirect'] = $this->getRedirectPage($items);
    
    return \Drupal::formBuilder()->getForm('Drupal\donation_field\Form\DonationFieldForm', $form_settings);
  
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

    // The text value has no text format assigned to it, so the user input
    // should equal the output, including newlines.
    return nl2br(Html::escape($item->value));
  }
  
  
  protected function getCurrentPage() {
    
    $current_request = $this->requestStack->getCurrentRequest();
    $uri = $current_request->getRequestUri();
    $router = \Drupal::service('router.no_access_checks');
    
    $route = $router->match($uri);
    $page = $route['_raw_variables']->all();
    
    return $page;
  }
  
  
  protected function getRedirectPage($items) {
    
    $page = [];
    foreach ($items as $item) {
      $id = $item->getValue()['target_id'];
      $entity_type = $this->fieldDefinition->getFieldStorageDefinition()->getSetting('target_type');
      $page[$entity_type] = $id;
    }
    return $page;
  }
  


}
