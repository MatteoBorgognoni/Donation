<?php

/**
 * @file
 * Contains donation.page.inc.
 *
 * Page callback for Donation entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Donation templates.
 *
 * Default template: donation.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - donation: Doantion object
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_donation_email(array &$variables) {
  // Fetch Donation Entity Object.
  /* @var \Drupal\donation\Entity\DonationInterface $donation */
  $donation = $variables['donation'];
}
