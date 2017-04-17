<?php

namespace Drupal\donation_stripe\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Stripe\Stripe;
use Stripe\Charge;
use Stripe\Error;

/**
 * Form controller for Donation edit forms.
 *
 * @ingroup donation
 */
class DonationStripeForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    
    $form['#attached']['library'] = ['donation_stripe/donation_stripe_js'];
    
    /* @var $entity \Drupal\donation\Entity\Donation */
    $form = parent::buildForm($form, $form_state);
    
    $entity = $this->entity;
    
    $this->paymentForm($form, $entity);
    
    return $form;
  }
  
  protected function paymentForm(array &$form, $entity) {
    
    $config = \Drupal::config('donation_stripe.settings');
    $key = $config->get('test_publishable_key');
    $form['#attached']['drupalSettings']['donation_stripe']['key'] = $key;
    
    $form['card_number'] = array(
      '#title' => t('Card Number'),
      '#type' => 'textfield',
      '#size' => 20,
      '#maxlength' => 20,
      '#attributes' => ['autocomplete' => 'off', 'name' => '', 'class' => ['card-number']],
  
    );
  
    $form['cvc'] = array(
      '#title' => t('CVC'),
      '#type' => 'textfield',
      '#size' => 4,
      '#maxlength' => 4,
      '#attributes' => ['autocomplete' => 'off', 'name' => '', 'class' => ['card-cvc']],
    );
  
    $months = range(1, 12);
    $month_options = [];
    foreach ($months as $month) {
      $month_options[$month] = $month;
    }
    $form['expiration_month'] = array(
      '#title' => t('Month'),
      '#type' => 'select',
      '#options' => $month_options,
      '#attributes' => ['autocomplete' => 'off', 'name' => '', 'class' => ['card-expiry-month']],
    );
    $years = range(date('Y'),date('Y') + 20);
    $years_options = [];
    foreach ($years as $year) {
      $year_options[$year] = $year;
    }
    $form['expiration_year'] = array(
      '#title' => t('Year'),
      '#type' => 'select',
      '#options' => $year_options,
      '#attributes' => ['autocomplete' => 'off', 'name' => '', 'class' => ['card-expiry-year']],
    );
  
    $form['stripeToken'] = array(
      '#type' => 'hidden',
      '#name' => 'stripeToken',
      '#value' => '',
      '#attributes' => ['class' => ['stripe-token']],
    );
  
    $form['errors'] = array(
      '#type' => 'markup',
      '#markup' => '<div id="payment-errors"></div>'
    );
  }
  
  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
  
    $entity = &$this->entity;
    
    $config = \Drupal::config('donation_stripe.settings');
    $key = $config->get('test_secret_key');
  
    $token = $form_state->getUserInput()['stripeToken'];
    ksm($token);
    ksm($form_state->getValues());
    
    Stripe::setApiKey($key);
  
    $amount = $entity->get('amount')->value;
    $currency = 'GBP';
    $email = $entity->get('mail')->value;

    try {
      $charge = Charge::create(array(
        'amount' => $amount, // Amount in cents!
        'currency' => $currency,
        'source' => $token,
      ));
    } catch (Error\Card $e) {
      //To do..  handling errors
    }
    
    ksm($charge);
    
    $status = parent::save($form, $form_state);

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Donation.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Donation.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.donation.canonical', ['donation' => $entity->id()]);
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
  
  

}
