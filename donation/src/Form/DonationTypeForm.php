<?php

namespace Drupal\donation\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\donation\Entity\DonationTypeInterface;

/**
 * Class DonationTypeForm.
 *
 * @package Drupal\donation\Form
 */
class DonationTypeForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $donation_type = $this->entity;
    
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $donation_type->label(),
      '#description' => $this->t("Label for the Donation type."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $donation_type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\donation\Entity\DonationType::load',
      ],
      '#disabled' => !$donation_type->isNew(),
    ];
  
//    $methodManager = \Drupal::service('plugin.manager.donation_method');
//    $available_methods = $methodManager->getDefinitions();
//
//    $methods = [];
//    foreach ($available_methods as $method) {
//      $methods[$method['id']] = $method['label']->render();
//    }
//
//    $default = '';
//    if (method_exists($donation_type, 'paymentMethod' )) {
//      $default = $donation_type->paymentMethod()['id'];
//    }
//
//    ksm($default);
//    // the payment method used in this Donation profile AKA plugin_id of type DonationMethod
//
//    $form['payment_method'] = [
//      '#type' => 'select',
//      '#title' => 'Payment Method',
//      '#options' => $methods,
//      '#default_value' => $default,
//    ];

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $donation_type = $this->entity;
    $status = $donation_type->save();
    $donation_type->set('type', trim($donation_type->id()));
    $donation_type->set('label', trim($donation_type->label()));
    
    ksm($donation_type);

    switch ($status) {
      case SAVED_NEW:
        donation_add_payment_field($donation_type);
        drupal_set_message($this->t('Created the %label Donation type.', [
          '%label' => $donation_type->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Donation type.', [
          '%label' => $donation_type->label(),
        ]));
    }
    $form_state->setRedirectUrl($donation_type->toUrl('collection'));
  }

}
