<?php

namespace Drupal\donation_field\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Session\SessionManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DonationFieldForm extends FormBase implements FormInterface {
  
  /**
   * Drupal\Core\Session\SessionManager definition.
   *
   * @var \Drupal\Core\Session\SessionManager
   */
  protected $sessionManager;
  
  public function __construct(
    SessionManager $session_manager
  ) {
    $this->sessionManager = $session_manager;
  }
  
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('session_manager')
    );
  }
  
  
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'donation_field_form';
  }
  
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, array $values = NULL) {
    
    $form['amount'] = [
      '#type' => 'number',
      '#placeholder' => $this->t('Enter amount here'),
      '#step' => 5,
      '#size' => 12,
      '#min' => 0,
      '#maxlength' => 5,
    ];
    
    $_SESSION['donation'] = $values;
    
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Donate'),
    ];
    ksm($_SESSION);
    return $form;
  }
  
  // TODO Form validation
  
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    
    $values = $form_state->getValues();
    $amount = $values['amount'];
    $_SESSION['donation']['amount'] = $amount;
    
    $url = \Drupal\Core\Url::fromRoute('donation.donation_page_build');
    $form_state->setRebuild(FALSE);
    $form_state->setRedirectUrl($url);
  }
  
}