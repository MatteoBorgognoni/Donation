<?php

namespace Drupal\donation;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Thanks entities.
 *
 * @ingroup donation
 */
class ThanksListBuilder extends EntityListBuilder {


  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Thanks ID');
    $header['title'] = $this->t('Title');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\donation\Entity\Thanks */
    $row['id'] = $entity->id();
    $row['title'] = Link::createFromRoute(
      $entity->label(),
      'entity.thanks.edit_form',
      ['thanks' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
