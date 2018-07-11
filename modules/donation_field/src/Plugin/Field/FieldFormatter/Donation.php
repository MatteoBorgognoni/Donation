<?php

namespace Drupal\donation_field\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormState;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityFormBuilderInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Plugin implementation of the 'donation field' formatter.
 *
 * Type - the field creator will select the donation profile to assign to this field and the Content type to redirect to
 * Widget - This field let the content creator choose a final page (entity reference) for the donation when creating a page
 * Formatter - It will display to the final user a donation amount form which will lead the user to the DonationPage.
 *
 * @FieldFormatter(
 *   id = "donation_field",
 *   label = @Translation("Donation field formatter"),
 *   field_types = {
 *     "donation_field"
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
   * The entity form builder.
   *
   * @var FormBuilderInterface
   */
  protected $formBuilder;
  
  
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
  public function __construct(
    $plugin_id,
    $plugin_definition,
    FieldDefinitionInterface $field_definition,
    array $settings,
    $label,
    $view_mode,
    array $third_party_settings,
    RequestStack $request_stack,
    EntityFormBuilderInterface $entity_form_builder,
    EntityStorageInterface $donation_storage,
    FormBuilderInterface $form_builder
  ) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
    $this->entityFormBuilder = $entity_form_builder;
    $this->donationStorage = $donation_storage;
    $this->requestStack = $request_stack;
    $this->formBuilder = $form_builder;
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
      $container->get('entity.manager')->getStorage('donation'),
      $container->get('form_builder')
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
    
    // Prepare Values to pass to the donation field form
    $form_settings = [];
    
    foreach ($items as $delta => $item) {
      
      // get profile from field definition settings
      $form_settings['donation_profile'] = $item->donation_type;
      
      // get page the field is attached to
      $form_settings['origin'] = $this->getOrigin();
      
      // get defined redirect page
      $form_settings['thanks_id'] = $item->thanks_id;
      
      //Build the form
      /** @var FormBuid $formBuilder */
      $form = $this->formBuilder->getForm('Drupal\donation_field\Form\DonationFieldForm', $form_settings);
      return $form;
    }
  }
  
  
  
  /**
   * Returns the Node the donation is attached to.
   * This method use \Drupal::request() as opposed to finding the entity the field is attached to, as the field can be attached to
   * a field_collection entity or paragraph entity which will make the origin meaningless
   *
   * @return \Drupal\node\Entity\Node $node
   *   An array containing the entity id to save as reference in the donation table keyed by entity type.
   *
   */
  
  protected function getOrigin() {
    
    $current_request = $this->requestStack->getCurrentRequest();
    
    $uri = $current_request->getRequestUri();
    $router = \Drupal::service('router.no_access_checks');
    $route = $router->match($uri);
    if (isset($route['node'])) {
      return $route['node'];
    } else {
      return NULL;
    }
  }
  
  /**
   * Returns the Page to redirect after the donation is completed.
   *
   * @return array $page
   *   An array containing the entity id to save as reference in the donation table keyed by entity type.
   *
   */
  
  protected function getCurrentPage() {
  
    $current_request = $this->requestStack->getCurrentRequest();
    
    $uri = $current_request->getRequestUri();
    $router = \Drupal::service('router.no_access_checks');
    $route = $router->match($uri);
    $page = $route['_raw_variables']->all();
    
    return $page;
  }
  
}