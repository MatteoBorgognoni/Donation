<?php

namespace Drupal\donation\Plugin\Field\FieldType;

use Drupal\Core\Field\Plugin\Field\FieldType\StringItem;

/**
 * Plugin implementation of the 'donation_amount' field type.
 *
 * @FieldType(
 *   id = "donation_amount",
 *   label = @Translation("Donation Amount"),
 *   description = @Translation("Amount field type for the Donation module"),
 *   default_widget = "donation_amount",
 *   default_formatter = "donation_amount",
 * )
 */
class DonationAmount extends StringItem {


}
