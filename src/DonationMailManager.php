<?php

namespace Drupal\donation;
use Drupal\Core\Mail\MailManager;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\Core\Language\LanguageManager;
use Drupal\Core\Link;
use Drupal\Component\Utility\Crypt;
use Drupal\Core\Site\Settings;
use stdClass;
use Drupal\donation\Entity\DonationInterface;
use Drupal\Core\Utility\Token;
use Drupal\donation\Entity\ThanksInterface;


/**
 * Class DonationMailManager.
 */
class DonationMailManager {
  
  use StringTranslationTrait;
  
  /**
   * Drupal\Core\Mail\MailManager definition.
   *
   * @var \Drupal\Core\Mail\MailManager
   */
  protected $mailManager;

  /**
   * Drupal\donation\DonationManager definition.
   *
   * @var \Drupal\donation\DonationManager
   */
  protected $donationManager;

  /**
   * Drupal\Core\Config\ConfigFactory definition.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $config;

  /**
   * Drupal\Core\Entity\EntityTypeManager definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * Drupal\Core\Language\LanguageManager definition.
   *
   * @var \Drupal\Core\Language\LanguageManager
   */
  protected $languageManager;
  
  /**
   * Drupal\Core\Utility\Token definition.
   *
   * @var \Drupal\Core\Utility\Token
   */
  protected $token;
  
  /**
   * Constructs a new QuoteEmailManager object.
   */
  public function __construct(
    MailManager $plugin_manager_mail,
    DonationManager $donation_manager,
    ConfigFactory $config_factory,
    EntityTypeManager $entity_type_manager,
    LanguageManager $language_manager,
    Token $token
  )
  {
    $this->mailManager = $plugin_manager_mail;
    $this->donationManager = $donation_manager;
    $this->config = $config_factory;
    $this->entityTypeManager = $entity_type_manager;
    $this->languageManager = $language_manager;
    $this->token = $token;
  }
  
  /**
   * {@inheritdoc}
   */
  public function sendThankYouEmail(DonationInterface $donation, ThanksInterface $thanks) {
  
    $module = 'donation';
    $key = 'thanks_mail';
    $langcode = $this->languageManager->getCurrentLanguage()->getId();
    
    $send = TRUE;
    
    $params = [
      'subject' => $this->token->replace($thanks->getEmailSubject(), ['donation' => $donation]),
      'from' => 'Sue Ryder <' . $this->config->get('system.site')->get('mail') . '>',
    ];
  
    $message = $this->token->replace($thanks->getEmailBody(), ['donation' => $donation]);

    $to = $donation->getEmail(TRUE);
    
    $params['message'] = $this->t($message);
    $result = $this->mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);
    
    if (!$result['result']) {
      return drupal_set_message('Error', 'error');
    } else {
      return $result;
    }
  }

}
