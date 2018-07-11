<?php

namespace Drupal\donation\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeBundleInfo;
use Drupal\Core\Url;
use Drupal\donation\Event\DonationFormLoad;
use Drupal\donation\DonationWrapper;
use Drupal\donation\Entity\DonationInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Entity\EntityFormBuilder;
use Drupal\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Drupal\Core\Entity\EntityRepository;
use GuzzleHttp\Cookie\SetCookie;
use Drupal\storage\StorageFactory;
use Drupal\donation\DonationManager;

/**
 * Class DonationPage.
 *
 * @package Drupal\donation\Controller
 */
class DonationPage extends ControllerBase {
  
 
  /**
   * Symfony\Component\HttpFoundation\RequestStack definition.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * Drupal\Core\Entity\EntityTypeManager definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;
  
  /**
   * Drupal\Core\Entity\EntityTypeBundleInfo definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeBundleInfo
   */
  protected $entityTypeBundleInfo;
  
  /**
   * Drupal\Core\Entity\EntityFormBuilder definition.
   *
   * @var \Drupal\Core\Entity\EntityFormBuilder
   */
  protected $entityFormBuilder;
  
  /**
   * Drupal\Core\Entity\EntityRepository definition.
   *
   * @var \Drupal\Core\Entity\EntityRepository
   */
  protected $entityRepository;
  
  /**
   * Drupal\Component\EventDispatcher\ContainerAwareEventDispatcher definition.
   *
   * @var \Drupal\Component\EventDispatcher\ContainerAwareEventDispatcher
   */
  protected $eventDispatcher;
  
  
  /**
   * Drupal\storage\StorageFactory definition.
   *
   * @var \Drupal\storage\StorageFactory;
   */
  protected $storageManager;
  
  
  /**
   * Drupal\donation\DonationManager definition.
   *
   * @var \Drupal\donation\DonationManager;
   */
  protected $donationManager;
  
  /**
   * {@inheritdoc}
   */
  public function __construct(
    RequestStack $request_stack,
    EntityTypeManager $entity_type_manager,
    EntityFormBuilder $entity_form_builder,
    ContainerAwareEventDispatcher $event_dispatcher,
    EntityTypeBundleInfo $entity_type_bundle_info,
    StorageFactory $storage_manager,
    DonationManager $donation_manager,
    EntityRepository $entityRepository
  ) {
    $this->requestStack = $request_stack;
    $this->entityTypeManager = $entity_type_manager;
    $this->entityFormBuilder = $entity_form_builder;
    $this->eventDispatcher = $event_dispatcher;
    $this->entityTypeBundleInfo = $entity_type_bundle_info;
    $this->storageManager = $storage_manager;
    $this->donationManager = $donation_manager;
    $this->entityRepository = $entityRepository;
  }
  
  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('request_stack'),
      $container->get('entity_type.manager'),
      $container->get('entity.form_builder'),
      $container->get('event_dispatcher'),
      $container->get('entity_type.bundle.info'),
      $container->get('storage.manager'),
      $container->get('donation.manager'),
      $container->get('entity.repository')
    );
  }
  
  /**
   * Build Donation page title.
   */
  public function setTitle() {
  
    $uuid = $this->requestStack->getCurrentRequest()->get('id');
    
    if($uuid) {
      /* @var DonationInterface $donation */
      $donation = $this->entityRepository->loadEntityByUuid('donation', $uuid);
    } else {
      $donation = FALSE;
    }
    
    
    if ($donation) {
      // Get donation profile label
      $type = $donation->getType();
      $bundle_info = $donation->getTypeInfo($type);
  
      // Create page title
      $page_title = $bundle_info['label'] . ' donation';
    } else {
      $page_title = 'Donation';
    }
    
    return $page_title;
  }
  
  

  /**
   * Build Donation page.
   */
  public function build($profile, $step) {
    $uuid = $this->requestStack->getCurrentRequest()->get('id');
    
    if($uuid) {
      /* @var DonationInterface $donation */
      $donation = $this->entityRepository->loadEntityByUuid('donation', $uuid);
    } else {
      $this->donationManager->log('alert', 'A donation has been attempted without query string.');
      $donation = FALSE;
    }
    
    if ($donation) {

      $event = new DonationFormLoad($donation);
      $this->eventDispatcher->dispatch(DonationFormLoad::EVENT_NAME, $event);

      $donationWrapper = new DonationWrapper($donation);
      // set form mode
      $form_mode = 'multistep';
  
      // Get form array
      $form = $donationWrapper->getForm($form_mode, $profile, $step);
      return [
        '#type' => 'markup',
        '#markup' => render($form),
      ];
    }
    else {
      // return an error message
      $markup = $this->t("It's not possible to take a donation now. Please retry in a few minutes. If the problem persists please contact us");
      return [
        '#type' => 'markup',
        '#markup' => $markup,
      ];
      
    }

  }

}
