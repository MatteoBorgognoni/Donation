<?php

namespace Drupal\donation\Event;

use Drupal\donation\Entity\DonationInterface;
use Symfony\Component\EventDispatcher\Event;
use Drupal\Core\Form\FormStateInterface;

/**
 * Event that is fired when a donation form page is loaded.
 */
class DonationFormSubmit extends Event {

  const EVENT_NAME = 'donation_form_submit';

  /**
   * The donation entity.
   *
   * @var \Drupal\user\UserInterface
   */
  public $donation;

  /**
   * The form array.
   *
   * @var array
   */
  public $form;

  /**
   * The FormState object.
   *
   * @var \Drupal\Core\Form\FormStateInterface
   */
  public $form_state;

  /**
   * DonationFormSubmit constructor.
   *
   * @param \Drupal\donation\Entity\DonationInterface $donation
   * @param $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   */
  public function __construct(DonationInterface &$donation, &$form, FormStateInterface &$form_state) {
    $this->donation = $donation;
    $this->form = $form;
    $this->form_state = $form_state;
  }

}