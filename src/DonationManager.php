<?php

namespace Drupal\donation;
use Drupal\Core\Entity\EntityFormBuilder;
use Drupal\Core\TempStore\TempStoreException;
use Drupal\donation\Entity\DonationInterface;
use Drupal\storage\StorageFactory;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\donation\Plugin\DonationMethodManager;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\Tests\Compiler\H;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\Core\Database\Connection;
use Drupal\Core\Session\SessionManager;
use Drupal\Core\Logger\LoggerChannelFactory;
use Drupal\Core\Session\AccountProxy;
use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;
use Drupal\Component\Utility\SortArray;
use Drupal\Component\Utility\Html;

/**
 * Class DonationManager.
 */
class DonationManager {
  
  /**
   * Drupal\Core\Database\Connection definition.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $db;
  /**
   * Drupal\storage\StorageFactory definition.
   *
   * @var \Drupal\storage\StorageFactory
   */
  protected $storageManager;
  /**
   * Drupal\storage\StorageFactory definition.
   *
   * @var \Drupal\storage\Storage
   */
  protected $storage;
  /**
   * Drupal\Core\Entity\EntityTypeManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;
  /**
   * Drupal\Core\Entity\EntityTypeBundleInfoInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeBundleInfoInterface
   */
  protected $entityTypeBundleInfo;
  /**
   * Drupal\donation\Plugin\DonationMethodManager definition.
   *
   * @var \Drupal\donation\Plugin\DonationMethodManager
   */
  protected $pluginManagerDonationMethod;
  /**
   * Drupal\Core\Config\ConfigFactoryInterface definition.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;
  /**
   * Drupal\Core\Logger\LoggerChannelFactory.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactory
   */
  protected $logger;
  /**
   * Drupal\Core\Session\SessionManager definition.
   *
   * @var \Drupal\Core\Session\SessionManager
   */
  protected $sessionManager;
  /**
   * Drupal\Core\Session\AccountInterface definition.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;
  /**
   * Drupal\Core\Entity\EntityFormBuilder definition.
   *
   * @var \Drupal\Core\Entity\EntityFormBuilder
   */
  protected $entityFormBuilder;
  /**
   * Symfony\Component\HttpFoundation\RequestStack definition.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;
  /**
   * Sort Array class.
   *
   * @var \Drupal\Component\Utility\SortArray
   */
  protected $sortArray;
  
  /**
   * Constructs a new DonationManager object.
   */
  public function __construct(
    Connection $database,
    StorageFactory $storage_manager,
    EntityTypeManagerInterface $entity_type_manager,
    EntityTypeBundleInfoInterface $entity_type_bundle_info,
    DonationMethodManager $plugin_manager_donation_method,
    ConfigFactoryInterface $config_factory,
    LoggerChannelFactory $logger,
    SessionManager $session_manager,
    AccountProxy $current_user,
    EntityFormBuilder $entity_form_builder,
    RequestStack $request_stack
  ) {
    $this->db = $database;
    $this->storageManager = $storage_manager;
    $this->storage = $this->storageManager->get('donation');
    $this->entityTypeManager = $entity_type_manager;
    $this->entityTypeBundleInfo = $entity_type_bundle_info;
    $this->pluginManagerDonationMethod = $plugin_manager_donation_method;
    $this->configFactory = $config_factory;
    $this->logger = $logger->get('donation');
    $this->sessionManager = $session_manager;
    $this->currentUser = $current_user;
    $this->entityFormBuilder = $entity_form_builder;
    $this->requestStack = $request_stack;
    $this->sortArray = new SortArray();
    $this->startSession();

  }
  
  public function startSession() {
    if ($this->currentUser->isAnonymous() && !isset($_SESSION['session_started'])) {
      $_SESSION['session_started'] = TRUE;
      $this->sessionManager->start();
    }
  }
  
  public function getDonation($id) {
    try {
      /** @var \Drupal\donation\Entity\Donation $donation */
      $donation = $this->entityTypeManager->getStorage('donation')->load($id);
    }
    catch (InvalidPluginDefinitionException $exception) {
      $donation = NULL;
      $this->log('error', $exception->getMessage());
    }
    return $donation;
  }
  
  public function viewDonation(DonationInterface $donation) {
    return $this->entityTypeManager->getViewBuilder('donation')->view($donation);
  }

  public function wrapper(DonationInterface $donation) {
    return new DonationWrapper($donation);
  }
  
  /**
   * Get Bundle settings
   *
   * @return \Drupal\Core\Config\ImmutableConfig
   */
  public function getBundleSettings($donation_type) {
    $settings = $this->configFactory->get('donation.donation_type.' . $donation_type);
    return $settings;
  }
  
  public function createDonation(array $values) {
    try {
      /** @var \Drupal\donation\Entity\Donation $donation */
      $donation = $this->entityTypeManager->getStorage('donation')->create($values);
    }
    catch (InvalidPluginDefinitionException $exception) {
      $donation = NULL;
      $this->log('error', $exception->getMessage());
    }
    return $donation;
  }

  
  public function getSteps($donation_type, $map = FALSE) {
    $config = $this->configFactory->get('core.entity_form_display.donation.' . $donation_type . '.multistep')->get('third_party_settings');
    $groups = isset($config['field_group']) ? $config['field_group'] : [];
    
    $steps = [];
    // count groups
    $count = count($groups);
    // if there are groups prepare $steps array
    $i = 1;
    if ($count > 0) {
      foreach ($groups as $key => $group) {
        $step_name = Html::cleanCssIdentifier(str_replace('group_', '',  $key));
  
        if($map) {
          $steps[$i] = $step_name;
          return $steps;
        }
        else {
          $steps[$step_name] = [
              'step' => str_replace('group_', '',  $key),
            ] + $group;
          $steps[$step_name]['number'] = $i;
        }
        $i++;
      }
      // Sort steps by weight and key them starting from 1
      uasort($steps, [$this->sortArray, 'sortByWeightElement']);
    }
    else {
      return FALSE;
    }
    return $steps;
  }
  
  
  public function setSteps($donation_type) {
    $steps = $this->getSteps($donation_type);
    
    if(count($steps) > 0) {
      $this->storageSet('steps', $steps);
      $this->storageSet('step_count', count($steps));
      return TRUE;
    }
    
    return FALSE;
  }

  public function getStep($steps, $number, $clean = FALSE) {
    $step = $steps[$number];
    return $step;
  }
  
  public function getStepName($steps, $number) {
    return $steps[$number];
  }
  
  public function getStepLabel($steps, $number) {
    $step = $steps[$number]['label'];
    return $step;
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
  
  public function storageClear() {
    return $this->storage->deleteAll();
  }
  
  /**
   * @return array Donation types
   * id => label
   */
  public function getDonationTypes() {
    $available_profiles = $this->entityTypeBundleInfo->getBundleInfo('donation');
    foreach ($available_profiles as $profile_id => $profile) {
      $donation_types[$profile_id] = $profile['label'];
    }
    return $donation_types;
  }
  
  /**
   * @return array Thanks pages
   * id => title
   */
  public function getThanksPages() {
    $query = $this->db->select('thanks_field_data', 't');
    $query->fields('t', ['id', 'title']);
    $query->orderBy('title', 'ASC');
    return $query->execute()->fetchAllKeyed('0', '1');
  }
  
  public function log($level, $message) {
    $this->logger->log($level, $message);
  }
  
}
