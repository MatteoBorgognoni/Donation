<?php

namespace Drupal\donation\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Donation Type entity.
 *
 * @ConfigEntityType(
 *   id = "donation_type",
 *   label = @Translation("Donation Type"),
 *   handlers = {
 *     "access" = "Drupal\donation\DonationTypeAccessControlHandler",
 *     "list_builder" = "Drupal\donation\DonationTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\donation\Form\DonationTypeForm",
 *       "edit" = "Drupal\donation\Form\DonationTypeForm",
 *       "delete" = "Drupal\donation\Form\DonationTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\donation\DonationTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "donation_type",
 *   admin_permission = "administer donation entities",
 *   bundle_of = "donation",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/donation_type/{donation_type}",
 *     "add-form" = "/admin/structure/donation_type/add",
 *     "edit-form" = "/admin/structure/donation_type/{donation_type}/edit",
 *     "delete-form" = "/admin/structure/donation_type/{donation_type}/delete",
 *     "collection" = "/admin/structure/donation_type"
 *   }
 * )
 */
class DonationType extends ConfigEntityBundleBase implements DonationTypeInterface {

  /**
   * The Donation type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Donation type label.
   *
   * @var string
   */
  protected $label;
  
  
  /**
   * The Payment Method id.
   *
   * @var string
   */
  protected $payment_method;
  
  public function paymentMethod() {
    return $this->payment_method;
  }
  
  protected function methodManager() {
    return \Drupal::service('plugin.manager.donation_method');
  }
  
}
