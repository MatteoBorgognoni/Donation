<?php

namespace Drupal\donation\Form;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\donation\Plugin\DonationMethodManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\ContentEntityFormInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\donation\Entity\Donation;

/**
 * Form controller for Donation edit forms.
 *
 * @ingroup donation
 */
class DonationForm extends ContentEntityForm implements ContentEntityFormInterface {
  
  
  /**
   * The Payment Method Manager.
   *
   * @var \Drupal\donation\Plugin\DonationMethodManager
   */
  protected $methodManager;
  
  /**
   * Constructs a ContentEntityForm object.
   *
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager.
   * @param \Drupal\Core\Entity\EntityTypeBundleInfoInterface $entity_type_bundle_info
   *   The entity type bundle service.
   * @param \Drupal\Component\Datetime\TimeInterface $time
   *   The time service.
   */
  public function __construct(EntityManagerInterface $entity_manager, EntityTypeBundleInfoInterface $entity_type_bundle_info = NULL, TimeInterface $time = NULL, DonationMethodManager $methodManager) {
    parent::__construct($entity_manager, $entity_type_bundle_info, $time);
    $this->methodManager = $methodManager;
  }
  
  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.manager'),
      $container->get('entity_type.bundle.info'),
      $container->get('datetime.time'),
      $container->get('plugin.manager.donation_method')
    );
  }
  
  /**
   * Get Bundle settings
   *
   * @return \Drupal\Core\Config\ImmutableConfig
   */
  public function getBundleSettings() {
    $donation_type = $this->entity->getType();
    $settings = \Drupal::config('donation.donation_type.' . $donation_type);
    return $settings;
  }
  
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\donation\Entity\Donation */
    $entity = $this->entity;
    
    $form = parent::buildForm($form, $form_state);
  
    // Set form variables for other use. see theme_suggestions
    $display = $this->getFormDisplay($form_state);
    $form['#mode'] = $display->getMode();
    $form['#donation_id'] = '';
    $form['#bundle'] = $display->getTargetBundle();
  
    // Set form id client-side variable
    $form['#attached']['drupalSettings']['donation']['form_id'] = $form['#attributes']['class'][0];
    
    return $form;
  }
  
  
  
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    
    $input = $form_state->getUserInput();
  
    // call payment method submit handler
    if (isset($input['payment_details'])) {
      $settings = $this->getBundleSettings();
      $method_id = $settings->get('payment_method');
      $this->methodManager->createInstance($method_id)->submitHandler($form, $form_state);
    }
    
  }
  
  
  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = &$this->entity;
  
    $entity->save();
    $id = $entity->id();
    
    $status = $entity->get('status')->value;
  
    $response = [];
  
    // If donation is new
    if ($status < 1) {
      
      // execute payment and get response from payment method
      $response = $this->pay($entity, $form, $form_state);
  
      // If response contains errors..
      if (isset($response['errors']) && !empty($response['errors']) && is_array($response['errors'])) {
        $message = $this->t("The following error(s) occurred: \n" );
        foreach ($response['errors'] as $e) {
          $message .=  $this->t("$e \n");
        }
        // Display error messages
        drupal_set_message(t($message), 'error');
        // Rebuild the form
        $form_state->setRebuild(TRUE);
      } else {
        // Prepare redirect
        // If redirect information available..
        if (isset($_SESSION['donation'][$id]['redirect'])) {
          // Get redirect
          $redirect = $_SESSION['donation'][$id]['redirect'];
          $redirect_entity_type = array_keys($redirect)[0];
          $route = 'entity.' . $redirect_entity_type . '.canonical';
          $form_state->setRedirect($route, $redirect);
        }
        // or redirect to the donation redirect page
        else {
          $route = 'donation.donation_redirect_page';
          $form_state->setRedirect($route, ['donation' => $entity->id()]);
        }
      }
    }
    // If editing Donation
    else {
      $form_state->setRedirect('entity.donation.canonical', ['donation' => $entity->id()]);
    }
  
    if (isset($_SESSION['donation'][$id])) {
      unset($_SESSION['donation'][$id]);
    }
  
    $entity->save();
  }
  

  
  /**
   * Returns an array of supported actions for the current entity form.
   *
   */
  protected function actions(array $form, FormStateInterface $form_state) {

    $actions['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Complete Donation'),
      '#submit' => array('::submitForm', '::save'),
    );
    
    return $actions;
  }
  
  
  /**
   * Execute the payment through the payment method plugin.
   *
   * @param \Drupal\donation\Entity\Donation $donation
   *   The donation Entity.
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The Form state passed as reference.
   */
  public function pay(Donation &$donation, array $form, FormStateInterface $form_state) {
    // Get bundle settings in order to retrieve the payment method
    $settings = $this->getBundleSettings();
    $method_id = $settings->get('payment_method');
    $method = $this->methodManager->createInstance($method_id);
  
    // Execute the payment and get data back
    $data = $method->execute($donation, $form, $form_state);
    
    return $data;
  }
  

}
