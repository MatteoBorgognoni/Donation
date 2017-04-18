<?php

namespace Drupal\donation\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Donation entities.
 */
class DonationViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // TODO: Implement Custom views fields
    //ksm($data);

    return $data;
  }

}
