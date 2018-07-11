<?php

namespace Drupal\donation;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeBundleInfo;
use Drupal\Core\Url;
use Drupal\donation\Entity\DonationInterface;
use Drupal\donation\Entity\ThanksInterface;
use Drupal\token\Token;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Entity\EntityFormBuilder;
use Drupal\Core\Session\SessionManager;
use Drupal\Core\Entity\EntityRepository;
use GuzzleHttp\Cookie\SetCookie;
use Drupal\donation\DonationManager;
use Drupal\Core\TempStore\TempStoreException;
use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;

/**
 * Class DonationWrapper.
 *
 * @package Drupal\donation
 */
class DonationWrapper {
  
  protected $donation;
  
  public $uuid;
  /**
   * The Donation Manager.
   *
   * @var \Drupal\donation\DonationManager
   */
  protected $donationManager;
  
  /** @var $storageManager \Drupal\storage\StorageFactory */
  protected $storageManager;
  
  /** @var $storage \Drupal\storage\Storage */
  protected $storage;
  
  /** @var $storage \Drupal\Core\Entity\EntityFormBuilder */
  protected $entityFormBuilder;
  
  /** @var $storage \Drupal\Core\Entity\EntityTypeManager */
  protected $entityTypeManager;
  
  /** @var $storage \Drupal\Core\Session\AccountProxy */
  protected $currentUser;
  
  /**
   * {@inheritdoc}
   */
  public function __construct(DonationInterface $donation) {
    $this->donation = $donation;
    $this->uuid = $donation->uuid();
    $this->donationManager = \Drupal::service('donation.manager');
    /** @var \Drupal\storage\StorageFactory $storageManager */
    $this->storageManager = \Drupal::service('storage.manager');
    $this->storage = $this->storageManager->get($this->uuid, 1800);
    /** @var EntityFormBuilder entityFormBuilder */
    $this->entityFormBuilder = \Drupal::service('entity.form_builder');
    /** @var EntityTypeManager entityTypeManager */
    $this->entityTypeManager = \Drupal::service('entity_type.manager');
  
    $this->currentUser = \Drupal::currentUser();
    
    $this->donationManager->startSession();
    
  }
  
  public function getForm($form_mode, $profile, $step) {
    
    if(!$this->storageGet('steps')) {
      $steps = $this->donationManager->getSteps($profile);
      $this->storageSet('steps', $steps);
    }
    else {
      $steps = $this->storageGet('steps');
    }

    if(!$this->storageGet('step_map')) {
      $step_map = [];
      foreach ($steps as $step_name => $step_data) {
        $step_map[$step_data['number']] = $step_name;
      }
      $this->storageSet('step_map', $step_map);
    }
    else {
      $step_map = $this->storageGet('step_map');
    }

    $this->storageSet('step', $step);
    $this->storageSet('step_number', $steps[$step]['number']);
    $this->storageSet('step_label', $steps[$step]['label']);
    
    if($steps[$step]['number'] == 1) {
      $this->storageSet('session_ended', FALSE);
    }

    $step_number = $steps[$step]['number'];
    $step_label = $steps[$step]['label'];
    $step_count = count($steps);
  
    $form = $this->entityFormBuilder->getForm($this->donation, $form_mode);
    
    $form['#mode'] = $form_mode;
    $form['#bundle'] = $profile;
    $form['#donation_id'] = $this->donation->id();
    $form['#donation_uuid'] = $this->donation->uuid();
    $form['#amount'] = $this->donation->getAmount();
    $form['#step_label'] = $step_label;
    $form['#step_number'] = $step_number;
    $form['#step_count'] = $step_count;


    $form['#previous_step'] = $step_number > 1 ? $step_map[$step_number - 1] : FALSE;
    $form['#next_step'] = $step_number < $step_count ? $step_map[$step_number + 1] : FALSE;
  
    // Set form id client-side variable
    $form['#attached']['drupalSettings']['donation']['form_id'] = $form['#attributes']['class'][0];
//    $all = $this->storage->getAll();
//    ksm($all, $form);
    return $form;
  }

  
  public function setPage(&$form, $steps, $step) {
    
    foreach ($steps as $step_name => $step_data) {
      if($step !== $step_name) {
        foreach ($step_data['children'] as $field_name) {
          unset($form[$field_name]);
        }
      }
    }
  }
  
  
  public function getThankYouPage($renderable = FALSE) {
    $thanks_id = $this->storageGet('thanks_id');
    if($thanks_id) {
      try {
        /** @var \Drupal\donation\Entity\ThanksInterface $thanks_page */
        $thanks_page = $this->entityTypeManager->getStorage('thanks')->load($thanks_id);
        if($renderable) {
          $thanks_page = $this->entityTypeManager->getViewBuilder('thanks')->view($thanks_page);
        }
      }
      catch (InvalidPluginDefinitionException $exception) {
        $thanks_page = NULL;
        $this->donationManager->log('error', $exception->getMessage());
      }
      return $thanks_page;
    }
  }
  
  public function storageSet($key, $value) {
    try {
      $this->storage->set($key, $value);
    }
    catch (TempStoreException $exception) {
      $this->log('error', $exception->getMessage());
    }
  }
  
  public function storageGet($key) {
    return $this->storage->get($key);
  }
  
  public function storageGetAll() {
    return $this->storage->getAll();
  }
  
  public function storageClear() {
    return $this->storage->deleteAll();
  }
  
}
