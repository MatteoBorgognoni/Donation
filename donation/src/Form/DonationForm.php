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
  
    return $form;
    
  }
  
  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = &$this->entity;

    parent::save($form, $form_state);
    $status = $entity->get('status')->value;
    
    // Set label;
    $line_item = $this->prepareLineItem();
    $entity->setLineItem($line_item);
    
    $response = [];
    if ($status < 1) {
      $payment = $this->pay($form, $form_state);

  
      $entity->save();
  
      if (isset($response['errors']) && !empty($response['errors']) && is_array($response['errors'])) {
        $message = '<div class="alert alert-error"><h4>Error!</h4>The following error(s) occurred:<ul>';
        foreach ($errors as $e) {
          $message .=  "<li>$e</li>";
        }
        $message .= '</ul></div>';
    
        $form_state->setError('payment_details', $message);
        $form_state->setRebuild(TRUE);
      } else {
        if (isset($_SESSION['donation'])) {
          $redirect = $_SESSION['donation']['redirect'];
          unset($_SESSION['donation']);
        } else {
          $redirect = ['donation' => $entity->id()];
        }
        $redirect_entity_type = array_keys($redirect)[0];
        $route = 'entity.' . $redirect_entity_type . '.canonical';
    
        $form_state->setRedirect($route, $redirect);
      }
    } else {
      $entity->save();
      $form_state->setRedirect('entity.donation.canonical', ['donation' => $entity->id()]);
    }


  }
  
  
  /**
   * Returns an array of supported actions for the current entity form.
   *
   */
  protected function actions(array $form, FormStateInterface $form_state) {
    // @todo Consider renaming the action key from submit to save. The impacts
    //   are hard to predict. For example, see
    //   \Drupal\language\Element\LanguageConfiguration::processLanguageConfiguration().
    $actions['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Complete Donation'),
      '#submit' => array('::submitForm', '::save'),
    );
    
    if (!$this->entity->isNew() && $this->entity->hasLinkTemplate('delete-form')) {
      $route_info = $this->entity->urlInfo('delete-form');
      if ($this->getRequest()->query->has('destination')) {
        $query = $route_info->getOption('query');
        $query['destination'] = $this->getRequest()->query->get('destination');
        $route_info->setOption('query', $query);
      }
      $actions['delete'] = array(
        '#type' => 'link',
        '#title' => $this->t('Delete'),
        '#access' => $this->entity->access('delete'),
        '#attributes' => array(
          'class' => array('button', 'button--danger'),
        ),
      );
      $actions['delete']['#url'] = $route_info;
    }
    
    return $actions;
  }
  
  
  
  public function pay(array $form, FormStateInterface $form_state) {
    
    $settings = $this->getBundleSettings();
    $method_id = $settings->get('payment_method');
    $method = $this->methodManager->createInstance($method_id);
    $data = $method->execute($this->entity, $form, $form_state);
    
    return $data;
  }
  
  public function prepareLineItem() {
    
    $line_item = '';
    $entity = &$this->entity;
    
    if(empty($entity->getLineItem())) {
      if(!empty($entity->getReference())) {
        $ref = $entity->getReference(TRUE);
        $type = strtoupper(substr($ref->getType(), 0 ,3));
        $label = strtoupper(substr($ref->label(), 0, 3));
        $line_item = $type . '-' . $label . '-' . str_pad($entity->id(), 6, 0, STR_PAD_LEFT);
      } else {
        $line_item = 'DON-' . str_pad($entity->id(), 6, 0);
      }
    }
    return $line_item;
  }
  
  
  

}
