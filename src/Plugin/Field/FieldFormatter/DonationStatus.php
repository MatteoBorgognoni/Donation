<?php

namespace Drupal\donation\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\Plugin\Field\FieldFormatter\BooleanFormatter;

/**
 * Plugin implementation of the 'donation_status' formatter.
 *
 * @FieldFormatter(
 *   id = "donation_status",
 *   label = @Translation("Donation status"),
 *   field_types = {
 *     "integer"
 *   }
 * )
 */
class DonationStatus extends BooleanFormatter {
  


}
