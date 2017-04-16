<?php

namespace Drupal\donation;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Routing\LinkGeneratorTrait;
use Drupal\Core\Url;

/**
 * Defines a class to build a listing of Donation entities.
 *
 * @ingroup donation
 */
class DonationListBuilder extends EntityListBuilder {

  use LinkGeneratorTrait;

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Donation ID');
    $header['line_item'] = $this->t('Line Item');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\donation\Entity\Donation */
    $row['id'] = $entity->id();
    $row['line_item'] = $this->l(
      $entity->label(),
      new Url(
        'entity.donation.edit_form', array(
          'donation' => $entity->id(),
        )
      )
    );
    return $row + parent::buildRow($entity);
  }

}
