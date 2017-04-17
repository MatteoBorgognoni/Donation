<?php

namespace Drupal\donation_stripe\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class SettingsForm.
 *
 * @package Drupal\donation_stripe\Form
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'donation_stripe.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('donation_stripe.settings');
    
    $form['mode'] = [
      '#type' => 'select',
      '#title' => 'Payment Mode',
      '#options' => ['test' => 'Test', 'live' => 'Live'],
      '#default_value' => $config->get('mode'),
    ];
    
    $form['test_settings'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Test Settings'),
    ];
    $form['test_settings']['test_secret_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Test Secret Key'),
      '#maxlength' => 32,
      '#size' => 32,
      '#default_value' => $config->get('test_secret_key'),
    ];
    $form['test_settings']['test_publishable_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Test Publishable Key'),
      '#maxlength' => 32,
      '#size' => 32,
      '#default_value' => $config->get('test_publishable_key'),
    ];
    $form['live_settings'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Live Settings'),
    ];
    $form['live_settings']['live_secret_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Live Secret Key'),
      '#maxlength' => 32,
      '#size' => 32,
      '#default_value' => $config->get('live_secret_key'),
    ];
    $form['live_settings']['live_publishable_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Live Publishable Key'),
      '#maxlength' => 32,
      '#size' => 32,
      '#default_value' => $config->get('live_publishable_key'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('donation_stripe.settings')
      ->set('mode', $form_state->getValue('mode'))
      ->set('test_secret_key', $form_state->getValue('test_secret_key'))
      ->set('test_publishable_key', $form_state->getValue('test_publishable_key'))
      ->set('live_secret_key', $form_state->getValue('live_secret_key'))
      ->set('live_publishable_key', $form_state->getValue('live_publishable_key'))
      ->save();
  }

}
