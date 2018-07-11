<?php

namespace Drupal\donation\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\donation\Entity\Donor;

/**
 * Provides an interface for defining Donation entities.
 *
 * @ingroup donation
 */
interface DonationInterface extends ContentEntityInterface, EntityChangedInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Donation type.
   *
   * @return string
   *   The Donation type.
   */
  public function getType();
  
  /**
   * Gets the Donation type settings.
   *
   * @return \Drupal\Core\Config\ImmutableConfig $settings
   *   The Donation type settings.
   */
  public function getTypeSettings();
  
  /**
   * Gets the Donation type info.
   *
   * @param string $type
   *   The Donation profile machine name.
   *
   * @return array
   *   DonationType bundle info
   */
  public function getTypeInfo($type);
  
  /**
   * Gets the Donation type payment method.
   *
   * @return \Drupal\donation\Plugin\DonationMethodInterface
   *   The Donation Payment method.
   */
  public function getMethodPlugin();
  
  /**
   * Gets the Donation Reference.
   *
   * @return string
   *   Unique reference for the Donation.
   */
  public function getReference();
  
  /**
   * Prepare a String to use as a label (Reference) for the donation
   *
   * @return string $reference
   */
  public function prepareReference();
  
  /**
   * Sets the Donation reference.
   *
   * @param string $reference
   *   The Donation reference.
   *
   * @return \Drupal\donation\Entity\DonationInterface
   *   The called Donation entity.
   */
  public function setReference($reference);
  
  
  /**
   * Gets the Donation Amount.
   *
   * @param boolean $raw
   *   If the amount returned should be formatted or not.
   *
   * @return string
   *   Amount of the Donation.
   */
  public function getAmount($raw = FALSE);
  
  /**
   * Gets the Formatted Donation Amount.

   * @return string
   *   Amount of the Donation.
   */
  public function getFormattedAmount();
  
  
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
   * @return \Drupal\core\entity\EntityInterface
   *   Original Donation Page.
   */
  public function getOrigin();
  
  
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
   * Get customer.
   *
   * @return array
   *   Customer.
   */
  public function getCustomer();

  /**
   * Set customer.
   *
   * @param array $customer
   *   Customer.
   */
  public function setCustomer(array $customer);
  
  /**
   * Get customer title.
   *
   * @return string
   *   Customer title.
   */
  public function getCustomerTitle();

  /**
   * Get first name.
   *
   * @return string
   *   First name.
   */
  public function getFirstName();

  /**
   * Get last name.
   *
   * @return string
   *   Last name.
   */
  public function getLastName();

  /**
   * Get full name.
   *
   * @return string
   *   Full name.
   */
  public function getFullName($title = FALSE);

  /**
   * Get customer email address.
   *
   * @param bool $raw
   *   TRUE for raw email address, FALSE for mailto link.
   *
   * @return string
   *   Email address.
   */
  public function getEmail($raw = FALSE);

  /**
   * Set customer email address.
   *
   * @param string $email
   *   Email address.
   */
  public function setEmail($email);

  /**
   * Get customer address.
   *
   * @return string
   *   Address.
   */
  public function getAddress();

  /**
   * Get postcode from customer address.
   *
   * @return string
   *   Postcode.
   */
  public function getPostCode();

  /**
   * Get telephone number from customer data.
   *
   * @return string
   *   Phone number.
   */
  public function getPhone();

  /**
   * Get line from customer address.
   *
   * @param int $number
   *   Address line number (1, 2 or 3).
   *
   * @return string
   *   Address line.
   */
  public function getAddressLine($number);

  /**
   * Get city from customer address.
   *
   * @return string
   *   City.
   */
  public function getCity();

  /**
   * Get country from customer address.
   *
   * @return string
   *   Country.
   */
  public function getCountry();

  /**
   * Get customer formatted address.
   *
   * @return string
   *   Formatted Address.
   */
  public function getFormattedAddress();
  
  /**
   * Get response values;.
   *
   * @return mixed
   *   Response values.
   */
  public function getResponseValues();
  
  /**
   * Get response by email.
   *
   * @param string $email
   *   Email address.
   * @param string $type
   *   Donation type.
   *
   * @return mixed
   *   Response.
   */
  public function getResponseByMail($email, $type);
  
  
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
