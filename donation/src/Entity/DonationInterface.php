<?php

namespace Drupal\donation\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Provides an interface for defining Donation entities.
 *
 * @ingroup donation
 */
interface DonationInterface extends  ContentEntityInterface, EntityChangedInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Donation type.
   *
   * @return string
   *   The Donation type.
   */
  public function getType();

  /**
   * Gets the Donation LineItem.
   *
   * @return string
   *   Unique lineitem id of the Donation.
   */
  public function getLineItem();

  /**
   * Sets the Donation lineitem id.
   *
   * @param string $line_item
   *   The Donation Lineitem id.
   *
   * @return \Drupal\donation\Entity\DonationInterface
   *   The called Donation entity.
   */
  public function setLineItem($line_item);
  
  
  /**
   * Gets the Donation Amount.
   *
   * @return string
   *   Amount of the Donation.
   */
  public function getAmount();
  
  /**
   * Sets the Donation Amount.
   *
   * @param string $amount
   *   The Donation Amount as entered in the donation form.
   *
   * @return \Drupal\donation\Entity\DonationInterface
   *   The called Donation entity.
   */
  public function setAmount($amount);
  
  /**
   * Gets the Currency code.
   *
   * @return string
   *   Currency code.
   */
  public function getCurrencyCode();
  
  /**
   * Sets the Currency Code.
   *
   * @param string $currency_code
   *   The Currency Code for this Donation.
   *
   * @return \Drupal\donation\Entity\DonationInterface
   *   The called Donation entity.
   */
  public function setCurrencyCode($amount);
  
  
  /**
   * Gets the Donation Page .
   *
   * @return string
   *   Reference.
   */
  public function getReference();
  
  /**
   * Sets a serialised string containing The Entity type and the Id of the page containing the donation.
   *
   * @param string $amount
   *   The Donation Amount as entered in the donation form.
   *
   * @return \Drupal\donation\Entity\DonationInterface
   *   The called Donation entity.
   */
  public function setReference($reference_array);
  
  
  /**
   * Gets the Payment Method id.
   *
   * @return string
   *   Payment Method plugin id.
   */
  public function getPaymentMethod();
  
  /**
   * Sets the Payment Method id.
   *
   * @param string $payment_method_id
   *   The Payment Method plugin id.
   *
   * @return \Drupal\donation\Entity\DonationInterface
   *   The called Donation entity.
   */
  public function setPaymentMethod($payment_method_id);
  
  
  /**
   * Gets the Donation creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Donation.
   */
  public function getCreatedTime();

  /**
   * Sets the Donation creation timestamp.
   *
   * @param int $timestamp
   *   The Donation creation timestamp.
   *
   * @return \Drupal\donation\Entity\DonationInterface
   *   The called Donation entity.
   */
  public function setCreatedTime($timestamp);


}
