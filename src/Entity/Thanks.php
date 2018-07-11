<?php

namespace Drupal\donation\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Thanks entity.
 *
 * @ingroup donation
 *
 * @ContentEntityType(
 *   id = "thanks",
 *   label = @Translation("Thank you page"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\donation\ThanksListBuilder",
 *     "views_data" = "Drupal\donation\Entity\ThanksViewsData",
 *     "translation" = "Drupal\donation\ThanksTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\donation\Form\ThanksForm",
 *       "add" = "Drupal\donation\Form\ThanksForm",
 *       "edit" = "Drupal\donation\Form\ThanksForm",
 *       "delete" = "Drupal\donation\Form\ThanksDeleteForm",
 *     },
 *     "access" = "Drupal\donation\ThanksAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\donation\ThanksHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "thanks",
 *   data_table = "thanks_field_data",
 *   translatable = TRUE,
 *   admin_permission = "administer thanks entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "title",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/donation/thanks/{thanks}",
 *     "add-form" = "/admin/content/thanks/add",
 *     "edit-form" = "/admin/content/thanks/{thanks}/edit",
 *     "delete-form" = "/admin/content/thanks/{thanks}/delete",
 *     "collection" = "/admin/content/thanks",
 *   },
 *   field_ui_base_route = "thanks.settings"
 * )
 */
class Thanks extends ContentEntityBase implements ThanksInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'user_id' => \Drupal::currentUser()->id(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    return $this->get('title')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setTitle($title) {
    $this->set('title', $title);
    return $this;
  }
  
  /**
   * {@inheritdoc}
   */
  public function getEmailEnabled() {
    return $this->get('email_enabled')->value;
  }
  
  /**
   * {@inheritdoc}
   */
  public function setEmailEnabled($enabled) {
    $this->set('email_enabled', $enabled);
    return $this;
  }
  
  /**
   * {@inheritdoc}
   */
  public function getEmailSubject() {
    return $this->get('email_subject')->value;
  }
  
  /**
   * {@inheritdoc}
   */
  public function setEmailSubject($subject) {
    $this->set('email_subject', $subject);
    return $this;
  }
  
  /**
   * {@inheritdoc}
   */
  public function getEmailBody() {
    return $this->get('email_body')->value;
  }
  
  /**
   * {@inheritdoc}
   */
  public function setEmailBody($body) {
    $this->set('email_body', $body);
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
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isPublished() {
    return (bool) $this->getEntityKey('status');
  }

  /**
   * {@inheritdoc}
   */
  public function setPublished($published) {
    $this->set('status', $published ? TRUE : FALSE);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Thank you page.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title'))
      ->setDescription(t('The title of the Thank you page.'))
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);
  
    $fields['email_enabled'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Enable email'))
      ->setTranslatable(TRUE)
      ->setCardinality(1)
      ->setDefaultValue(0)
      ->setDisplayOptions('form', array(
        'type' => 'boolean_checkbox',
        'weight' => -3,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', FALSE);
    
    $fields['email_subject'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Email subject'))
      ->setDescription(t('The Subject for the email to send.'))
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -2,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', FALSE);
  
    $fields['email_body'] = BaseFieldDefinition::create('text_with_summary')
      ->setLabel(t('Email body'))
      ->setDescription(t('The Body for the email to send.'))
      ->setDisplayOptions('form', [
        'type' => 'text_textarea_with_summary_token',
        'weight' => -1,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', FALSE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Publishing status'))
      ->setDescription(t('A boolean indicating whether the Thank you page is published.'))
      ->setDefaultValue(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => 30,
      ]);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

}
