<?php

namespace Drupal\donation\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Entity\EntityFormBuilder;
use Drupal\Core\Session\SessionManager;



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
   * Drupal\Core\Entity\EntityFormBuilder definition.
   *
   * @var \Drupal\Core\Entity\EntityFormBuilder
   */
  protected $entityFormBuilder;
  
  /**
   * Drupal\Core\Session\SessionManager definition.
   *
   * @var \Drupal\Core\Session\SessionManager
   */
  protected $sessionManager;
  
  /**
   * {@inheritdoc}
   */
  public function __construct(RequestStack $request_stack, EntityTypeManager $entity_type_manager, EntityFormBuilder $entity_form_builder, SessionManager $session_manager) {
    $this->requestStack = $request_stack;
    $this->entityTypeManager = $entity_type_manager;
    $this->entityFormBuilder = $entity_form_builder;
    $this->sessionManager = $session_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('request_stack'),
      $container->get('entity_type.manager'),
      $container->get('entity.form_builder'),
      $container->get('session_manager')
    );
  }

  /**
   * Build.
   *
   * @return string
   *   Return Hello string.
   */
  public function build() {
    
    $settings = $_SESSION['donation'];
    ksm($settings);
    $values = [
      'type' => $settings['donation_profile'],
    ];
    
    $donation = $this->entityTypeManager->getStorage('donation')->create($values);
    $donation->setAmount($settings['amount']);
    $donation->setReference($settings['reference']);
    $donation->setPaymentMethod($settings['donation_method']);
    
    $form = $this->entityFormBuilder->getForm($donation);
    $form['#attached']['drupalSettings']['donation']['form_id'] = $form['#id'];
    
    
    return [
      '#type' => 'markup',
      '#markup' => render($form),
    ];
  }

}
