<?php

namespace Drupal\donation;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Provides a listing of Donation type entities.
 */
class DonationTypeListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Donation Profile');
    $header['id'] = $this->t('Machine name');
   // $header['payment_method'] = $this->t('Payment Method');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
  
   
    $row['label'] = $entity->label();
    $row['id'] = $entity->id();
    //$row['payment_method'] = $entity->paymentMethodLabel();
    
    return $row + parent::buildRow($entity);

    
  }

}
