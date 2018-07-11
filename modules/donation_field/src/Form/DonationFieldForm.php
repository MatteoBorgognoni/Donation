<?php

namespace Drupal\donation_field\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormInterface;
use Drupal\Component\Utility\Html;
use Drupal\Core\Url;
use Drupal\donation\DonationManager;
use Drupal\donation\DonationWrapper;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Component\Utility\Random;

class DonationFieldForm extends FormBase implements FormInterface {
  
  /**
   * The Donation Manager.
   *
   * @var \Drupal\donation\DonationManager
   */
  protected $donationManager;
  
  /**
   * Class constructor.
   */
  public function __construct(DonationManager $donation_manager) {
    $this->donationManager = $donation_manager;
  }
  
  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    return new static(
    // Load the service required to construct this class.
      $container->get('donation.manager')
    );
  }
  
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    $random = $this->getRandomString(4);
    return 'donation_field_form';//z_' . $random;
  }
  
  public function getRandomString($length = 6) {
    $characters = '0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
  }
  
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, array $values = NULL) {
  
    $profile = $values['donation_profile'];
    $thanks_id = $values['thanks_id'];
    /** @var \Drupal\node\Entity\Node $origin */
    $origin = $values['origin'];
    
    //Prevents the form from being cached.
    $form_state->disableCache();
    
    // Set a new form id to make multiple forms available in the same page
//    $build = $form_state->getBuildInfo();
//    $build['form_id'] = $build['form_id'] . '_' . $profile;
//    $form_state->setBuildInfo($build);
//    $form['#form_id'] =  $this->getFormId() . '_' . $profile;
//    $form['#id'] = str_replace('_' , '-', $build['form_id']);
//    $form['#attributes']['id'] = str_replace('_' , '-', $build['form_id']);
    
    // Form elements
    $form['amount'] = [
      '#type' => 'number',
      '#required' => TRUE,
      '#title' => $this->t('Amount'),
      '#placeholder' => $this->t('Enter amount here'),
      '#size' => 12,
      '#min' => 0,
      '#step' => 0.01,
      '#maxlength' => 8,
      '#attributes' => ['id' => 'amount-' . $profile ],
    ];
    
    $form['profile'] = [
      '#type' => 'hidden',
      '#value' => $profile,
    ];
  
    $form['thanks_id'] = [
      '#type' => 'hidden',
      '#value' => $thanks_id,
    ];
    
    $form['origin'] = [
      '#type' => 'hidden',
      '#value' => !is_null($origin) ? $origin->id() : 0,
    ];
    
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Donate'),
      '#name' => $profile,
      '#attributes' => ['id' => 'submit-' . $profile ],
      '#button_type' => 'primary',
      '#submit' => ['::submitForm'],
    ];
 
    return $form;
  }
  
 
  
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    // get all values
    $values = $form_state->getUserInput();
    
    // Create donation object of passed profile/bundle
    /* @var \Drupal\donation\Entity\donation $donation */
    $donation = $this->donationManager->createDonation(['type' => $values['profile']]);
    
    // Set raw amount into Donation object
    if (isset($values['amount'])) {
      $donation->setAmount($values['amount'], TRUE);
    }
  
    // Set origin if available
    if (isset($values['origin'])) {
      $donation->set('origin', $values['origin']);
    }
    // Save now in order to get info on abandoned donations and and obtain an id to use for storing an unique session array
    $donation->save();
    
    $profile = Html::cleanCssIdentifier(strtolower($values['profile']));
  
    $donationWrapper = new DonationWrapper($donation);
    
    // Create a session array for the donation and save redirect info
    if (isset($values['thanks_id'])) {
      $donationWrapper->storageSet('thanks_id', $values['thanks_id']);
    }
  
    $steps = $this->donationManager->getSteps($values['profile'], TRUE);
    $first_step = $this->donationManager->getStepName($steps,1);
    
    // Redirect to the donation page.
    $route = 'donation.donation_page_build';
    $parameters = [
      'step' => $first_step,
      'profile' => $profile,
    ];
    $options = [
      'query' => ['id' => $donation->uuid()],
    ];
    
    $form_state->setRebuild(FALSE);
    $form_state->setRedirect($route, $parameters, $options);
  }
  
}