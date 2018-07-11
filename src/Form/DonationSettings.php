<?php

namespace Drupal\donation\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Mail\MailManager;

/**
 * Class DonationSettings.
 *
 * @package Drupal\donation\Form
 */
class DonationSettings extends ConfigFormBase {

  /**
   * Drupal\Core\Mail\MailManager definition.
   *
   * @var \Drupal\Core\Mail\MailManager
   */
  protected $pluginManagerMail;
  /**
   * Constructs a new DonationSettings object.
   */
  public function __construct(
    ConfigFactoryInterface $config_factory,
      MailManager $plugin_manager_mail
    ) {
    parent::__construct($config_factory);
        $this->pluginManagerMail = $plugin_manager_mail;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('plugin.manager.mail')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'donation.donation_settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'donation_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('donation.donation_settings');
    
    $email_config = $config->get('donation_email');
    
    $form['donation_email'] = [
      '#type' => 'details',
      '#title' => $this->t('Email Configuration'),
      '#tree' => TRUE,
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
    ];
    $form['donation_email']['subject'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Subject'),
      '#maxlength' => 64,
      '#size' => 64,
      '#default_value' => isset($email_config['subject']) ? $email_config['subject'] : '',
    ];


    $form['donation_email']['from'] = [
      '#type' => 'textfield',
      '#title' => $this->t('From'),
      '#description' => $this->t('Enter the details for the "from" email element in the format "@ex_2" <br>
                                         If left empty the "from" will be: "@ex_1". ', ['@ex_1' => 'Site name <site@email>', '@ex_2' => 'Sender Name <sender@email>.']),
      '#maxlength' => 64,
      '#size' => 64,
      '#default_value' => isset($email_config['from']) ? $email_config['from'] : '',
    ];
  
    $form['donation_redirect'] = [
      '#type' => 'details',
      '#title' => $this->t('Redirect Page Configuration'),
      '#tree' => TRUE,
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
    ];
  
  
    $redirect_config = $config->get('donation_redirect');

    $form['donation_redirect']['body'] = [
      '#type' => 'text_format',
      '#title' => $this->t('Body'),
      '#rows' => 15,
      '#format' => 'full_html',
      '#default_value' => isset($redirect_config['body']) ? $redirect_config['body']['value'] : '',
    ];
    
    // Add the token tree UI.
    //    $form['donation_email']['token_tree'] = array(
    //      '#theme' => 'token_tree_link',
    //      '#token_types' => ['donation'],
    //      '#show_restricted' => TRUE,
    //      '#global_types' => TRUE,
    //      '#weight' => 90,
    //    );
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

    $this->config('donation.donation_settings')
      ->set('donation_email', $form_state->getValue('donation_email'))
      ->set('donation_redirect', $form_state->getValue('donation_redirect'))
      ->save();
  }

}
