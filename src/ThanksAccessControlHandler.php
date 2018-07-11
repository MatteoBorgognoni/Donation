<?php

namespace Drupal\donation;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Thanks entity.
 *
 * @see \Drupal\donation\Entity\Thanks.
 */
class ThanksAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\donation\Entity\ThanksInterface $entity */
    switch ($operation) {
      case 'view':
        return AccessResult::allowedIfHasPermission($account, 'view thanks entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit thanks entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete thanks entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add thanks entities');
  }

}
