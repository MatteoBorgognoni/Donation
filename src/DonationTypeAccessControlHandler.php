<?php

namespace Drupal\donation;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines the access control handler for the donation type entity type.
 *
 * @see \Drupal\donation\Entity\DonationType
 */
class DonationTypeAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    switch ($operation) {
      case 'view':
        return AccessResult::allowedIfHasPermission($account, 'access content');

      default:
        return parent::checkAccess($entity, $operation, $account);

    }
  }

}
