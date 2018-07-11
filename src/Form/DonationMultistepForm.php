<?php

namespace Drupal\donation\Form;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\donation\DonationManager;
use Drupal\donation\DonationWrapper;
use Drupal\donation\Plugin\DonationMethodManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\ContentEntityFormInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Drupal\donation\Entity\Donation;
use Drupal\donation\DonationMailManager;
use Drupal\Core\Extension\ModuleHandler;
use Drupal\Component\Utility\SortArray;
use Drupal\Component\Utility\Html;
use Drupal\donation\Event\DonationFormSubmit;

/**
 * Form controller for Donation edit forms.
 *
 * @ingroup donation
 */
class DonationMultistepForm extends ContentEntityForm implements ContentEntityFormInterface {

  /**
   * The Donation Manager.
   *
   * @var \Drupal\donation\DonationManager
   */
  protected $donationManager;
  
  /**
   * The Donation Manager.
   *
   * @var \Drupal\donation\DonationMailManager
   */
  protected $mailManager;

  /**
   * Drupal\Component\EventDispatcher\ContainerAwareEventDispatcher definition.
   *
   * @var \Drupal\Component\EventDispatcher\ContainerAwareEventDispatcher
   */
  protected $eventDispatcher;

  /**
   * The Payment Method Manager.
   *
   * @var \Drupal\donation\Plugin\DonationMethodManager
   */
  protected $methodManager;
  
  /**
   * Module Handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandler
   */
  protected $moduleHandler;
  
  
  /**
   * Sort Array class.
   *
   * @var \Drupal\Component\Utility\SortArray
   */
  protected $sortArray;
  
  /**
   * @var \Drupal\donation\DonationWrapper;
   */
  protected $wrapper;
  
  /**
   * Groups defined in display form
   *
   * @var array
   *
   */
  public $steps;


  /**
   * Current step name
   *
   * @var string
   *
   */
  public $step;

  /**
   * Current step number
   *
   * @var integer
   *
   */
  public $stepNumber;


  /**
   * Previous step
   *
   * @var string
   *
   */
  public $previousStep;

  /**
   * Next step
   *
   * @var string
   *
   */
  public $nextStep;
  
  
  /**
   * @var string
   *
   * Number of steps
   *
   */
  public $count;
  
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
  public function __construct(
    EntityManagerInterface $entity_manager,
    EntityTypeBundleInfoInterface $entity_type_bundle_info = NULL,
    TimeInterface $time = NULL,
    DonationManager $donationManager,
    DonationMailManager $donationMailManager,
    DonationMethodManager $methodManager,
    ContainerAwareEventDispatcher $event_dispatcher,
    ModuleHandler $moduleHandler
  ) {
    parent::__construct($entity_manager, $entity_type_bundle_info, $time);
    $this->donationManager = $donationManager;
    $this->mailManager = $donationMailManager;
    $this->methodManager = $methodManager;
    $this->eventDispatcher = $event_dispatcher;
    $this->moduleHandler = $moduleHandler;
    $this->sortArray = new SortArray();
    $this->wrapper = NULL;
    $this->step = NULL;
    $this->stepNumber = NULL;
    $this->previousStep = FALSE;
    $this->nextStep = FALSE;
    
  }
  
  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.manager'),
      $container->get('entity_type.bundle.info'),
      $container->get('datetime.time'),
      $container->get('donation.manager'),
      $container->get('donation.mail_manager'),
      $container->get('plugin.manager.donation_method'),
      $container->get('event_dispatcher'),
      $container->get('module_handler')
    );
  }
  
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    $form_id = $this->entity->getEntityTypeId();
    if ($this->entity->getEntityType()->hasKey('bundle')) {
      $form_id .= '_' . $this->entity->bundle();
    }
    if ($this->operation != 'default') {
      $form_id = $form_id . '_' . $this->operation;
    }
    return $form_id . '_form';
  }
  
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $donation \Drupal\donation\Entity\Donation */
    $donation = $this->entity;

    $this->wrapper = new DonationWrapper($donation);
    $this->steps = $this->wrapper->storageGet('step_map');
    $this->count = count($this->steps);
    $this->step = $this->wrapper->storageGet('step');
    $this->stepNumber = $this->wrapper->storageGet('step_number');

    $steps_data = $this->wrapper->storageGet('steps');
    $this->previousStep = $this->stepNumber > 1 ? $this->steps[$this->stepNumber - 1] : FALSE;
    $this->nextStep = $this->stepNumber < $this->count ? $this->steps[$this->stepNumber + 1] : FALSE;
    $this->previousStep = $this->stepNumber > 1 ? $this->steps[$this->stepNumber - 1] : FALSE;
    $this->nextStep = $this->stepNumber < $this->count ? $this->steps[$this->stepNumber + 1] : FALSE;
    
    $form = parent::buildForm($form, $form_state);

    
    $this->wrapper->setPage($form, $steps_data, $this->step);
    return $form;
  }
  
  
  
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    /** @var \Drupal\donation\Entity\DonationInterface entity */
    $this->entity = $this->buildEntity($form, $form_state);
    $this->updateChangedTime($this->entity);
    $id = $this->entity->id();

    $event = new DonationFormSubmit($this->entity, $form, $form_state);
    $this->eventDispatcher->dispatch(DonationFormSubmit::EVENT_NAME, $event);

    $input = $form_state->getUserInput();
    
    $step = $this->getStep();
    
    // Get clicked button
    $op = $form_state->getTriggeringElement()['#name'];

    switch ($op) {
      case 'back':
        // If is not the first step
        if ($this->getStep() > 1) {
          // Go back one step
          $this->setStep($step - 1);
          $this->setAction($step - 1, $form, $form_state);
          //$form_state->setRebuild(TRUE);
        } else {
          // If an origin node is available redirect to it
          if($this->entity->get('origin')) {
            $target_id = $this->entity->get('origin')->getValue()[0]['target_id'];
            $redirect_entity_type = 'node';
            $form_state->setRebuild(FALSE);
            $route = 'entity.' . $redirect_entity_type . '.canonical';
            $redirect = [$redirect_entity_type => $target_id];
            unset($_SESSION['donation'][$id]);
            $form_state->setRedirect($route, $redirect);
          }
          // Or redirect to referer
          else {
            // TODO redirect to referer
          }
        }
        break;
      case 'next':
        //$form_state->setRebuild(TRUE);
        // if this step containd the payment_details field call payment method submit handler
        if (isset($input['payment_details'])) {
          /* @var \Drupal\donation\Plugin\DonationMethodInterface $method */
          $method = $this->entity->getMethodPlugin();
          $method->submitHandler($this->entity, $form, $form_state);
        }
        $this->entity->save();
        // Set next step
        $this->setAction($step + 1, $form, $form_state);
        break;
      case 'save':
        break;
    }
    
  }
  
  
  
  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    /* @var $donation \Drupal\donation\Entity\Donation */
    $donation = &$this->entity;
    $this->wrapper = new DonationWrapper($donation);
    
    $donation->save();
    
    $status = $donation->get('status')->value;
    
    // If donation is new
    if ($status < 1) {
      // execute payment and get response from payment method
      $response = $this->pay($donation, $form, $form_state);
    
      // If response contains errors..
      if (isset($response['errors']) && !empty($response['errors']) && is_array($response['errors'])) {
        $message = $this->t("The following error(s) occurred: \n" );
        foreach ($response['errors'] as $e) {
          $message .=  $this->t("$e \n");
        }
  
        $this->setStep(1);
        $this->setAction(1, $form, $form_state);
        
        $profile = \Drupal::routeMatch()->getParameter('profile');
        
        $route = 'donation.donation_page_build';
        $parameters = [
          'profile' => $profile,
          'step' => 'donation-confirmation',
        ];
        // Display error messages
        drupal_set_message(t($message), 'error');
        
      }
      // If success
      else {
        $thanks = $this->wrapper->getThankYouPage();
        // Allow payment methods to specify a redirect
        if($redirect = $this->wrapper->storageGet('redirect')) {
          $route = $redirect['route'];
          $parameters = $redirect['parameters'];
        }
        else {
          $route = 'donation.donation_redirect_page';
          $parameters = [];
        }
        
      }
      
    }
    // If editing Donation
    else {
      $route = 'entity.donation.canonical';
      $parameters = ['donation' => $donation->id()];
    }
  
    $options = [
      'query' => [
        'id' => $donation->uuid(),
      ],
    ];
  
    $form_state->setRedirect($route, $parameters, $options);
  
    $donation->save();
    
  }
  

  
  /**
   * Returns an array of supported actions for the current entity form.
   *
   */
  protected function actions(array $form, FormStateInterface $form_state) {
    if (!is_null($this->steps)) {
      $step = $this->getStep();
      if ($step == 1) {
    
        $actions['next'] = array(
          '#type' => 'submit',
          '#value' => $this->t('Next'),
          '#submit' => ['::submitForm'],
          '#name' => 'next',
          '#id' => 'edit-submit',
          '#button_type' => 'primary',
        );
    
//        $actions['back'] = array(
//          '#type' => 'submit',
//          '#value' => $this->t('Back'),
//          '#submit' => array('::submitForm'),
//          '#name' => 'back',
//          '#limit_validation_errors' => [],
//          '#id' => 'edit-back',
//          '#attributes' => [
//            'class' => [],
//          ],
//        );
      }
      // If last step we want back and save button
      elseif ($step == $this->count) {
    
        $actions['submit'] = array(
          '#type' => 'submit',
          '#value' => $this->t('Complete donation'),
          '#submit' => array('::submitForm', '::save'),
          '#name' => 'save',
          '#id' => 'edit-submit',
          '#button_type' => 'primary',
        );
    
        $actions['back'] = array(
          '#type' => 'submit',
          '#value' => $this->t('Back'),
          '#submit' => array('::submitForm'),
          '#name' => 'back',
          '#limit_validation_errors' => [],
          '#id' => 'edit-back',
          '#attributes' => [
            'class' => ['button--secondary'],
          ],
          '#button_type' => 'secondary',
        );
    
      }
      // Default next and back button
      else {
    
        $actions['next'] = array(
          '#type' => 'submit',
          '#value' => $this->t('Next'),
          '#submit' => ['::submitForm'],
          '#name' => 'next',
          '#id' => 'edit-submit',
          '#button_type' => 'primary',
        );
        
        $actions['back'] = array(
          '#type' => 'submit',
          '#value' => $this->t('Back'),
          '#submit' => array('::submitForm'),
          '#name' => 'back',
          '#limit_validation_errors' => [],
          '#id' => 'edit-back',
          '#button_type' => 'secondary',
          '#attributes' => [
            'class' => ['button--secondary'],
          ],
        );
    
      }
    } else {
      $actions['submit'] = array(
        '#type' => 'submit',
        '#value' => $this->t('Complete Donation'),
        '#submit' => array('::submitForm', '::save'),
        '#name' => 'save',
        '#id' => 'edit-submit',
        '#button_type' => 'primary',
      );
  
      $actions['back'] = array(
        '#type' => 'submit',
        '#value' => $this->t('Back'),
        '#submit' => array('::submitForm'),
        '#name' => 'back',
        '#limit_validation_errors' => [],
        '#id' => 'edit-back',
        '#attributes' => [
          'class' => ['button--secondary'],
        ],
      );
    }
    


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
    /* @var \Drupal\donation\Plugin\DonationMethodInterface $method */
    $method = $donation->getMethodPlugin();
    
    // Execute the payment and get data back
    $data = $method->execute($donation, $form, $form_state);
    
    return $data;
  }
  
  
  /**
   * Set step (integer) in Donation session array
   *
   * @param $step
   */
  protected function setStep($step) {
    $this->entity->set('step', $step);
    $this->entity->save();
  }
  
  /**
   * Return false
   *
   * @return integer $step
   */
  protected function getStep() {
    return $this->wrapper->storageGet('step_number');
  }
  
  public function setAction($step, array &$form, FormStateInterface &$form_state) {
    /* @var \Drupal\donation\Entity\DonationInterface $donation */
    $donation = &$this->entity;

    //$url = \Drupal::urlGenerator()->generateFromRoute(
    $step_name = $this->steps[$step];

    $profile = Html::cleanCssIdentifier($donation->getType());
    
    $route = 'donation.donation_page_build';
    $parameters = [
      'step' => $step_name,
      'profile' => $profile,
    ];
    $options = [
      'query' => ['id' => $donation->uuid()],
      'https' => TRUE,
    ];
    
    $form_state->setRedirect($route, $parameters, $options);
  }
  
}
