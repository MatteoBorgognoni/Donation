<?php

namespace Drupal\donation;

use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Routing\LinkGeneratorTrait;
use Drupal\Core\Url;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Routing\RedirectDestinationInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines a class to build a listing of Donation entities.
 *
 * @ingroup donation
 */
class DonationListBuilder extends EntityListBuilder {

  use LinkGeneratorTrait;
  
  /**
   * The date formatter service.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;
  
  /**
   * The redirect destination service.
   *
   * @var \Drupal\Core\Routing\RedirectDestinationInterface
   */
  protected $redirectDestination;
  
  
  /**
   * Constructs a new NodeListBuilder object.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The entity storage class.
   * @param \Drupal\Core\Datetime\DateFormatterInterface $date_formatter
   *   The date formatter service.
   * @param \Drupal\Core\Routing\RedirectDestinationInterface $redirect_destination
   *   The redirect destination service.
   */
  public function __construct(EntityTypeInterface $entity_type, EntityStorageInterface $storage, DateFormatterInterface $date_formatter, RedirectDestinationInterface $redirect_destination) {
    parent::__construct($entity_type, $storage);
    
    $this->dateFormatter = $date_formatter;
    $this->redirectDestination = $redirect_destination;
  }
  
  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('entity.manager')->getStorage($entity_type->id()),
      $container->get('date.formatter'),
      $container->get('redirect.destination')
    );
  }
  
  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
  
    $header = [
      'reference' => $this->t('Donation ID'),
      'type' => [
        'data' => $this->t('Type'),
        'class' => [RESPONSIVE_PRIORITY_MEDIUM],
      ],
      'email' => [
        'data' => $this->t('Email'),
        'class' => [RESPONSIVE_PRIORITY_LOW],
      ],
      'status' => $this->t('Status'),
      'amount' => [
        'data' => $this->t('Amount'),
        'class' => [RESPONSIVE_PRIORITY_MEDIUM],
      ],
      'changed' => [
        'data' => $this->t('Date'),
        'class' => [RESPONSIVE_PRIORITY_LOW],
      ],
    ];
    return $header + parent::buildHeader();
    
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\donation\Entity\Donation */
  
    $langcode = $entity->language()->getId();
    $uri = $entity->urlInfo();
    $options = $uri->getOptions();
    $options += ($langcode != LanguageInterface::LANGCODE_NOT_SPECIFIED && isset($languages[$langcode]) ? ['language' => $languages[$langcode]] : []);
    $uri->setOptions($options);
  
    $row['reference']['data'] = [
      '#type' => 'link',
      '#title' => $entity->label(),
      '#url' => $uri,
    ];
    $row['type'] = $entity->getType();
    $row['email']['data'] = [
      '#markup' => $entity->getEmail(),
    ];
    $row['status'] = $entity->get('status')->value;
    $row['amount'] = $entity->getFormattedAmount(FALSE);
    
    $row['changed'] = $this->dateFormatter->format($entity->getChangedTime(), 'short');
  
    $row['operations']['data'] = $this->buildOperations($entity);
    return $row + parent::buildRow($entity);
  }
  
  /**
   * {@inheritdoc}
   */
  protected function getDefaultOperations(EntityInterface $entity) {
    $operations = parent::getDefaultOperations($entity);
    
    $destination = $this->redirectDestination->getAsArray();
    foreach ($operations as $key => $operation) {
      $operations[$key]['query'] = $destination;
    }
    return $operations;
  }
  
}
