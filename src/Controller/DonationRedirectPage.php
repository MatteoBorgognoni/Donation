<?php

namespace Drupal\donation\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeBundleInfo;
use Drupal\donation\Entity\DonationInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Entity\EntityFormBuilder;
use Drupal\Core\Entity\EntityRepository;
use Drupal\donation\DonationManager;
use Drupal\donation\DonationMailManager;
use Drupal\Core\Routing\RedirectDestination;
use Drupal\donation\DonationWrapper;

/**
 * Class DonationRedirectPage.
 *
 * @package Drupal\donation\Controller
 */
class DonationRedirectPage extends ControllerBase {
  
  
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
   * Drupal\Core\Entity\EntityRepository definition.
   *
   * @var \Drupal\Core\Entity\EntityRepository
   */
  protected $entityRepository;
  
  /**
   * Drupal\donation\DonationManager definition.
   *
   * @var \Drupal\donation\DonationManager
   */
  protected $donationManager;
  
  /**
   * Drupal\donation\DonationMailManager definition.
   *
   * @var \Drupal\donation\DonationMailManager
   */
  protected $mailManager;
  
  /**
   * Drupal\Core\Routing\RedirectDestination definition.
   *
   * @var \Drupal\Core\Routing\RedirectDestination
   */
  protected $redirect;
  
  /**
   * {@inheritdoc}
   */
  public function __construct(
    RequestStack $request_stack,
    EntityTypeManager $entity_type_manager,
    EntityRepository $entityRepository,
    DonationManager $donation_manager,
    DonationMailManager $donation_mail_manager,
    EntityTypeBundleInfo $entity_type_bundle_info,
    RedirectDestination $redirect_destination
  ) {
    $this->requestStack = $request_stack;
    $this->entityTypeManager = $entity_type_manager;
    $this->entityRepository = $entityRepository;
    $this->donationManager = $donation_manager;
    $this->mailManager = $donation_mail_manager;
    $this->entityTypeBundleInfo = $entity_type_bundle_info;
    $this->redirect = $redirect_destination;
    
  }
  
  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('request_stack'),
      $container->get('entity_type.manager'),
      $container->get('entity.repository'),
      $container->get('donation.manager'),
      $container->get('donation.mail_manager'),
      $container->get('entity_type.bundle.info'),
      $container->get('redirect.destination')
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
      $this->donationManager->log('alert', 'A donation has been attempted without query string.');
      $donation = FALSE;
    }
  
    if ($donation) {
      $donationWrapper = new DonationWrapper($donation);

      /** @var \Drupal\donation\Entity\ThanksInterface $thanks_page */
      $thanks_page = $donationWrapper->getThankYouPage();
      if(!is_null($thanks_page)) {
        if($thanks_page->getEmailEnabled()) {
          $this->mailManager->sendThankYouEmail($donation, $thanks_page);
        }
        return $thanks_page->getTitle();
      }
      else {
        $page_title = 'Thank you';
      }
      
    }
    else {
      $page_title = 'Thank you';
    }
    
    return $page_title;
  }
  
  /**
   * Build Donation page.
   */
  public function build() {
  
    $uuid = $this->requestStack->getCurrentRequest()->get('id');
  
    if($uuid) {
      /* @var DonationInterface $donation */
      $donation = $this->entityRepository->loadEntityByUuid('donation', $uuid);
    } else {
      $this->donationManager->log('alert', 'A donation has been attempted without query string.');
      $donation = FALSE;
    }
  
    if ($donation) {
      $donation->set('status', 1);
      $donation->save();
      $donationWrapper = new DonationWrapper($donation);
      $build = $donationWrapper->getThankYouPage(TRUE);
      if($build) {
        return $build;
      }
      else {
        $build = [
          '#theme' => 'thanks_default',
          '#variables' => [
            'donation' => $donation,
          ],
        ];
      }

      //$donationWrapper->storageClear();
      return $build;
    }
    
    return $this->redirect('<front>');
    
  }
  
}
