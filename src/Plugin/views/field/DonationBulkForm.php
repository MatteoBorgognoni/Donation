<?php

namespace Drupal\donation\Plugin\views\field;

use Drupal\system\Plugin\views\field\BulkForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines a Donation operations bulk form element.
 *
 * @ViewsField("donation_bulk_form")
 */
class DonationBulkForm extends BulkForm {
  
  /**
   * {@inheritdoc}
   */
  protected function emptySelectedMessage() {
    return $this->t('No content selected.');
  }

}
