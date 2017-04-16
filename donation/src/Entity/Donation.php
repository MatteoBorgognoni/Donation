<?php

namespace Drupal\donation\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Donation entity.
 *
 * @ingroup donation
 *
 * @ContentEntityType(
 *   id = "donation",
 *   label = @Translation("Donation"),
 *   bundle_label = @Translation("Donation Type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\donation\DonationListBuilder",
 *     "views_data" = "Drupal\donation\Entity\DonationViewsData",
 *     "translation" = "Drupal\donation\DonationTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\donation\Form\DonationForm",
 *       "add" = "Drupal\donation\Form\DonationForm",
 *       "edit" = "Drupal\donation\Form\DonationForm",
 *       "delete" = "Drupal\donation\Form\DonationDeleteForm",
 *     },
 *     "access" = "Drupal\donation\DonationAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\donation\DonationHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "donation",
 *   data_table = "donation_field_data",
 *   translatable = TRUE,
 *   admin_permission = "administer donation entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "bundle" = "type",
 *     "label" = "line_item",
 *     "langcode" = "langcode",
 *     "uuid" = "uuid",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/donation/{donation}",
 *     "add-page" = "/donation/add",
 *     "add-form" = "/donation/add/{donation_type}",
 *     "edit-form" = "/admin/structure/donation/{donation}/edit",
 *     "delete-form" = "/admin/structure/donation/{donation}/delete",
 *     "collection" = "/admin/content/donation",
 *   },
 *   bundle_entity_type = "donation_type",
 *   field_ui_base_route = "entity.donation_type.edit_form"
 * )
 */
class Donation extends ContentEntityBase implements DonationInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
  }

  /**
   * {@inheritdoc}
   */
  public function getType() {
    return $this->bundle();
  }

  /**
   * {@inheritdoc}
   */
  public function getLineItem() {
    return $this->get('line_item')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setLineItem($line_item) {
    $this->set('line_item', $line_item);
    return $this;
  }
  
  /**
   * {@inheritdoc}
   */
  public function getAmount() {
    return $this->get('amount')->value / 100;
  }
  
  /**
   * {@inheritdoc}
   */
  public function setAmount($amount) {
    $this->set('amount', $amount * 100);
    return $this;
  }
  
  
  /**
   * {@inheritdoc}
   */
  public function getReference() {
    $reference = unserialize($this->get('reference')->value);
    return $reference;
  }
  
  /**
   * {@inheritdoc}
   */
  public function setReference($reference_array) {
    
    $reference = [];
    $entity_type = array_keys($reference_array)[0];
    $id = $reference_array[$entity_type];
    $reference['entity_type'] = $entity_type;
    $reference['id'] = $id;
    
    $this->set('reference', serialize($reference));
    return $this;
  }
  

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }


  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);
  
    $fields['line_item'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Line Item'))
      ->setDescription(t('The Line Item name for the Donation'))
      ->setSettings(array(
        'max_length' => 50,
        'text_processing' => 0,
      ));
//      ->setDefaultValue('')
//      ->setDisplayOptions('view', array(
//        'type' => 'string',
//        'weight' => -10,
//        'label' => 'hidden',
//      ))
//      ->setDisplayOptions('form', array(
//        'type' => 'string_textfield',
//        'weight' => -10,
//      ))
//      ->setDisplayConfigurable('form', TRUE)
//      ->setDisplayConfigurable('view', TRUE);
    
    
    $fields['reference'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Reference'))
      ->setDescription(t('The serialised string of the page receiving the Donation. in the format EntityType, EntityId'))
      ->setSettings(array(
        'max_length' => 255,
        'text_processing' => 0,
      ));
//      ->setDefaultValue('')
//      ->setDisplayOptions('view', array(
//        'type' => 'string',
//        'weight' => -10,
//        'label' => 'hidden',
//      ))
//      ->setDisplayOptions('form', array(
//        'type' => 'string_textfield',
//        'weight' => -10,
//      ))
//      ->setDisplayConfigurable('form', TRUE)
//      ->setDisplayConfigurable('view', TRUE);
    
 
  
    $fields['amount'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Amount'))
      ->setDescription(t('An integer indicating the amount of the Donation in cents.'))
      ->setSettings(array(
        'min' => 0,
        'text_processing' => 0,
      ));
//      ->setDefaultValue(0)
//      ->setDisplayOptions('view', array(
//        'label' => 'above',
//        'type' => 'number_integer',
//        'weight' => -7,
//      ))
//      ->setDisplayOptions('form', array(
//        'type' => 'number',
//        'weight' => -7,
//      ))
//      ->setDisplayConfigurable('form', TRUE)
//      ->setDisplayConfigurable('view', TRUE);
  
    
    $fields['currency_code'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Currency Code'))
      ->setDescription(t('The Currency Code for the Donation'))
      ->setSettings(array(
        'max_length' => 4,
        'text_processing' => 0,
      ))
      ->setDefaultValue('GBP');
//      ->setDisplayOptions('view', array(
//        'type' => 'string',
//        'weight' => -6,
//      ))
//      ->setDisplayOptions('form', array(
//        'type' => 'string_textfield',
//        'weight' => -6,
//      ))
//      ->setDisplayConfigurable('form', TRUE)
//      ->setDisplayConfigurable('view', TRUE);
  
  
    $fields['status'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Payment status'))
      ->setDescription(t('An integer indicating the payment status of the Donation.'))
      ->setSettings(array(
        'max' => 8,
        'min' => 0,
        'text_processing' => 0,
      ))
      ->setDefaultValue(0);
//      ->setDisplayOptions('view', array(
//        'label' => 'above',
//        'type' => 'number_integer',
//        'weight' => -5,
//      ))
//      ->setDisplayOptions('form', array(
//        'type' => 'number',
//        'weight' => -5,
//      ))
//      ->setDisplayConfigurable('form', FALSE)
//      ->setDisplayConfigurable('view', TRUE);

  
    $fields['customer'] = BaseFieldDefinition::create('map')
      ->setLabel(t('Customer Info'))
      ->setDescription(t('Fieldset containing customer Info'))
      ->setTranslatable(FALSE)
      ->setDisplayOptions('view', array(
        'type' => 'donation_customer',
        'weight' => -4,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'donation_customer',
        'weight' => -4,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);
  
    $fields['payment_details'] = BaseFieldDefinition::create('donation_payment')
      ->setLabel(t('Payment Details'))
      ->setDescription(t('Payment Details'))
      ->setTranslatable(FALSE)
      ->setDisplayOptions('view', array(
        'type' => 'donation_payment',
      ))
      ->setDisplayOptions('form', array(
        'type' => 'donation_payment',
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);
  
    
    $fields['gift_aid'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Gift Aid'))
      ->setDescription(t('A boolean indicating whether the Donation contains Gift Aid.'))
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', array(
        'type' => 'boolean',
        'weight' => -2,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'boolean_checkbox',
        'weight' => -2,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);
    
    
//    $fields['first_name'] = BaseFieldDefinition::create('string');
//    $fields['last_name'] = BaseFieldDefinition::create('string');
//    $fields['email'] = BaseFieldDefinition::create('string');


    
    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

}
