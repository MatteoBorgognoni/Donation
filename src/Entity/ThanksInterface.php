<?php

namespace Drupal\donation\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Thank you pages.
 *
 * @ingroup donation
 */
interface ThanksInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Thank you page title.
   *
   * @return string
   *   Title of the Thank you page.
   */
  public function getTitle();

  /**
   * Sets the Thank you page title.
   *
   * @param string $title
   *   The Thank you page title.
   *
   * @return \Drupal\donation\Entity\ThanksInterface
   *   The called Thank you page.
   */
  public function setTitle($title);
  
  /**
   * Gets the Thank you email_enabled.
   *
   * @return boolean
   *   If the thank you email is enabled.
   */
  public function getEmailEnabled();
  
  /**
   * Sets the Thank you email_enabled.
   *
   * @param boolean $enabled
   *   The Thank you email_enabled flag.
   *
   * @return \Drupal\donation\Entity\ThanksInterface
   *   The called Thank you page.
   */
  public function setEmailEnabled($enabled);
  
  /**
   * Gets the Thank you email subject.
   *
   * @return string
   *   The thank you email subject.
   */
  public function getEmailSubject();
  
  /**
   * Sets the Thank you email subject.
   *
   * @param string $subject
   *   The Thank you email subject.
   *
   * @return \Drupal\donation\Entity\ThanksInterface
   *   The called Thank you page.
   */
  public function setEmailSubject($subject);
  
  /**
   * Gets the Thank you email body.
   *
   * @return string
   *   The thank you email body.
   */
  public function getEmailBody();
  
  /**
   * Sets the Thank you email body.
   *
   * @param string $body
   *   The Thank you email body.
   *
   * @return \Drupal\donation\Entity\ThanksInterface
   *   The called Thank you page.
   */
  public function setEmailBody($body);

  /**
   * Gets the Thank you page creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Thank you page.
   */
  public function getCreatedTime();

  /**
   * Sets the Thank you page creation timestamp.
   *
   * @param int $timestamp
   *   The Thank you page creation timestamp.
   *
   * @return \Drupal\donation\Entity\ThanksInterface
   *   The called Thank you page entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Thank you page published status indicator.
   *
   * Unpublished Thank you page are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Thank you page is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Thank you page.
   *
   * @param bool $published
   *   TRUE to set this Thank you page to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\donation\Entity\ThanksInterface
   *   The called Thanks entity.
   */
  public function setPublished($published);

}
