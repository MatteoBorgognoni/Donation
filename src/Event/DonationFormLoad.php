<?php

namespace Drupal\donation\Event;

use Drupal\donation\Entity\DonationInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Event that is fired when a donation form page is loaded.
 */
class DonationFormLoad extends Event {

  const EVENT_NAME = 'donation_form_load';

  /**
   * The donation entity.
   *
   * @var \Drupal\user\UserInterface
   */
  public $donation;

  /**
   * Constructs the object.
   *
   * @param \Drupal\donation\Entity\DonationInterface $donation
   *   The donation of the user logged in.
   */
  public function __construct(DonationInterface $donation) {
    $this->donation = $donation;
  }

}