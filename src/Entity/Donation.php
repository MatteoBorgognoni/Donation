<?php

namespace Drupal\donation\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityTypeBundleInfo;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Mail\Plugin\Mail\PhpMail;
use Drupal\Core\Site\Settings;
use Drupal\Component\Utility\Html;

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
 *     "storage" = "Drupal\donation\Entity\DonationStorage",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\donation\DonationListBuilder",
 *     "views_data" = "Drupal\donation\Entity\DonationViewsData",
 *     "translation" = "Drupal\donation\DonationTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\donation\Form\DonationForm",
 *       "multistep" = "Drupal\donation\Form\DonationMultistepForm",
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
 *     "label" = "reference",
 *     "langcode" = "langcode",
 *     "uuid" = "uuid",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/donation/{donation}",
 *     "uuid" = "/donation/{donation}",
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
  public function getType() {
    return $this->bundle();
  }
  
  /**
   * {@inheritdoc}
   */
  public function getTypeSettings() {
    $donation_type = $this->getType();
    $settings = \Drupal::config('donation.donation_type.' . $donation_type);
    return $settings;
  }
  
  /**
   * {@inheritdoc}
   */
  public function getTypeInfo($type) {
    
    $bundle_info = \Drupal::service('entity_type.bundle.info')->getBundleInfo('donation');
    return $bundle_info[$type];
    
  }
  
  /**
   * {@inheritdoc}
   */
  public function getMethodPlugin() {
    $settings = $this->getTypeSettings();
    $method_id = $settings->get('payment_method');
    $method = \Drupal::service('plugin.manager.donation_method')->createInstance($method_id);
    
    return $method;
  }

  
  /**
   * {@inheritdoc}
   */
  public function getReference() {
    return $this->get('reference')->value;
  }
  
  /**
   * {@inheritdoc}
   */
  public function prepareReference($id = NULL) {
    $reference = '';
    
    if (is_null($id)) {
      $id = $this->id();
    }
    
    if(empty($this->label())) {
      // If the Donation has an origin node
      if(!empty($this->getOrigin())) {
        // Get the origin
        $origin = $this->getOrigin();
        // Get 3 chars of content type
        $type = iconv('UTF-8', 'ASCII//TRANSLIT' , $origin->getType());
        $type = strtoupper(substr($type, 0 ,3));
        // Get 3 chars of node label
        $label = iconv('UTF-8', 'ASCII//TRANSLIT' , $origin->label());
        $label = Html::cleanCssIdentifier($label);
        $label = strtoupper(substr($label, 0, 3));
        // Prepare string
        $reference = $type . '-' . $label . '-' . str_pad($id, 6, 0, STR_PAD_LEFT);
        
      } else {
        // Prepare string without node info
        $reference = 'DON-' . str_pad($id, 6, 0, STR_PAD_LEFT);
      }
    }
    
    $reference = str_replace(' ', '', $reference);
    return $reference;
  }
  
  
  /**
   * {@inheritdoc}
   */
  public function setReference($reference) {
    $this->set('reference', $reference);
    return $this;
  }
  
  /**
   * {@inheritdoc}
   */
  public function getAmount($raw = FALSE) {
    switch ($raw) {
      case TRUE:
        return (float) $this->get('amount')->value;
        break;
      case FALSE:
        return (float) $this->get('amount')->value / 100;
        break;
    }
  }
  
  
  /**
   * {@inheritdoc}
   */
  public function getFormattedAmount() {
    //TODO internationalization
    
    $amount = $this->getAmount();
    
    setlocale(LC_MONETARY, 'en_GB.UTF-8');
    $amount = money_format('%n', $amount);
    return $amount;
  }
  
  
  /**
   * {@inheritdoc}
   */
  public function setAmount($amount, $raw = FALSE) {
    switch ($raw) {
      case TRUE:
        $this->set('amount', $amount * 100);
        break;
      case FALSE:
        $this->set('amount', $amount);
        break;
    }
    
    return $this;
  }
  
  /**
   * {@inheritdoc}
   */
  public function getCurrencyCode() {
    return $this->get('currency_code')->value;
  }
 
  
  /**
   * {@inheritdoc}
   */
  public function setCurrencyCode($currency_code) {
    $this->set('currency_code', $currency_code);
    return $this;
  }
  
 
  /**
   * {@inheritdoc}
   */
  public function getOrigin() {
    $origin = $this->get('origin')->referencedEntities();
    if (isset($origin[0])) {
      return $origin[0];
    } else {
      return NULL;
    }
  }
  
  
  /**
   * {@inheritdoc}
   */
  public function getPaymentMethod() {
    return $this->get('payment_details')->value;
  }
  
  /**
   * {@inheritdoc}
   */
  public function setPaymentMethod($payment_method_id) {
    $this->set('payment_details', $payment_method_id);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCustomer() {
    $customer = $this->get('customer')->getValue();
    return isset($customer[0]) ? $customer[0] : NULL;
  }


  /**
   * {@inheritdoc}
   */
  public function getCustomerTitle() {
    $customer = $this->getCustomer();
    if(!$customer) {
      return NULL;
    }
    $title = isset($customer['title']) ? $customer['title'] . ' ' : '';
    return $title;
  }

  /**
   * {@inheritdoc}
   */
  public function getFirstName() {
    $customer = $this->getCustomer();
    if(!$customer) {
      return NULL;
    }
    $first_name = isset($customer['first_name']) ? $customer['first_name'] . ' ' : '';
    return $first_name;
  }

  /**
   * {@inheritdoc}
   */
  public function getLastName() {
    $customer = $this->getCustomer();
    if(!$customer) {
      return NULL;
    }
    $last_name = isset($customer['last_name']) ? $customer['last_name'] . ' ' : '';
    return $last_name;
  }


  /**
   * {@inheritdoc}
   */
  public function getFullName($title = FALSE) {
    $customer = $this->getCustomer();
    if(!$customer) {
      return NULL;
    }
    $first_name = isset($customer['first_name']) ? $customer['first_name'] . ' ' : '';
    $last_name = isset($customer['last_name']) ? $customer['last_name'] : '';
    $full_name = $first_name . $last_name;

    if ($title) {
      $title = $this->getCustomerTitle();
      $full_name = !empty($title) ? $title . ' ' . $full_name : $full_name;
    }

    return $full_name;
  }


  /**
   * {@inheritdoc}
   */
  public function getEmail($raw = FALSE) {
    $customer = $this->getCustomer();
    if(!$customer) {
      return NULL;
    }
    $email = isset($customer['email']) ? $customer['email'] : '';
    switch ($raw) {
      case TRUE:
        $email = !empty($email) ? $email : '';
        break;
      case FALSE:
        $email = !empty($email) ? '<a href="mailto:' . $email . '">' . $email . '</a>' : '';
        break;
    }
    return $email;
  }

  /**
   * {@inheritdoc}
   */
  public function setCustomer(array $customer) {
    $customer = $this->set('customer', $customer);
    return $customer;
  }

  /**
   * {@inheritdoc}
   */
  public function setEmail($email) {
    $customer = $this->getCustomer();
    $customer['email'] = $email;
    $this->setCustomer($customer);
  }

  /**
   * {@inheritdoc}
   */
  public function getPhone() {
    $customer = $this->getCustomer();
    $phone = isset($customer['phone']) ? $customer['phone'] : '';
    return $phone;
  }

  /**
   * {@inheritdoc}
   */
  public function getAddress() {
    $address = $this->get('address')->getValue();
    return isset($address[0]) ? $address[0] : NULL;
  }


  /**
   * {@inheritdoc}
   */
  public function getPostCode() {
    $address = $this->getAddress();
    $post_code = isset($address['post_code']) ? trim(strtoupper($address['post_code'])) : '';
    return $post_code;
  }

  /**
   * {@inheritdoc}
   */
  public function getAddressLine($number) {
    $address = $this->getAddress();
    $line = isset($address['address_line_' . $number]) ? $address['address_line_' . $number] : '';
    return $line;
  }

  /**
   * {@inheritdoc}
   */
  public function getCity() {
    $address = $this->getAddress();
    $city = isset($address['city']) ? $address['city'] : '';
    return $city;
  }

  /**
   * {@inheritdoc}
   */
  public function getCountry() {
    $address = $this->getAddress();
    $country = isset($address['country']) ? $address['country'] : '';
    return $country;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormattedAddress() {

    $address_data = $this->getAddress();
    $address = [];

    foreach ($address as $key => $address_item) {
      if(!empty($address_item)) {
        $address[$key] = $address_item . '<br>';
      }
    }

    return implode($address);
  }

  /**
   * {@inheritdoc}
   */
  public function getResponseValues() {
    return $this->get('response')->getValue()[0];
  }
  

  /**
   * {@inheritdoc}
   */
  public function getResponseByMail($email, $type) {
    $select = \Drupal::database()->select('donation_field_data', 'd')
      ->distinct()
      ->fields('d', ['response'])
      ->condition('type', $type, '=')
      ->condition('customer__email', $email);

    $result = $select->execute()->fetchField();
    return unserialize($result);
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
  
    $fields['reference'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Reference'))
      ->setDescription(t('The reference name for the Donation'))
      ->setSettings(array(
        'max_length' => 50,
        'text_processing' => 0,
      ))
      ->setDisplayOptions('view', array(
        'type' => 'string',
        'weight' => -10,
        'label' => 'hidden',
      ))
      ->setDisplayConfigurable('view', TRUE);
    
  
    $fields['origin'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Origin'))
      ->setCardinality(1)
      ->setDescription(t('The Page the donation is attached to.'))
      ->setSetting('target_type', 'node')
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayConfigurable('view', TRUE);
    
  
    $fields['amount'] = BaseFieldDefinition::create('donation_amount')
      ->setLabel(t('Amount'))
      ->setCardinality(1)
      ->setRequired(TRUE)
      ->setSettings(array(
        'min' => 0,
        'text_processing' => 0,
      ))
      ->setDisplayOptions('view', array(
        'type' => 'donation_amount',
        'weight' => -9,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'donation_amount',
        'weight' => -9,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);
    
    
    $fields['currency_code'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Currency Code'))
      ->setDescription(t('The Currency Code for the Donation'))
      ->setCardinality(1)
      ->setSettings(array(
        'max_length' => 4,
        'text_processing' => 0,
      ))
      ->setDisplayOptions('view', array(
        'type' => 'hidden',
        'weight' => -9,
      ))
      ->setDefaultValue('GBP');
  
  
    $fields['status'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Payment status'))
      ->setDescription(t('An integer indicating the payment status of the Donation.'))
      ->setSettings(array(
        'max' => 8,
        'min' => 0,
        'text_processing' => 0,
      ))
      ->setDefaultValue(0)
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'donation_status',
        'weight' => -5,
      ))
      ->setDisplayConfigurable('view', TRUE);
  
    $fields['step'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Step'))
      ->setDescription(t('An integer indicating the last step reached during the donation process.'))
      ->setSettings(array(
        'max' => 999,
        'min' => 1,
        'text_processing' => 0,
      ))
      ->setDefaultValue(1)
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'donation_step',
        'weight' => 5,
      ))
      ->setDisplayConfigurable('view', TRUE);
  
    $fields['customer'] = BaseFieldDefinition::create('donation_customer')
      ->setLabel(t('Donor details'))
      ->setTranslatable(FALSE)
      ->setCardinality(1)
      ->setDisplayOptions('view', array(
        'type' => 'donation_customer',
        'weight' => -3,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'donation_customer',
        'weight' => -3,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['address'] = BaseFieldDefinition::create('generic_address')
      ->setLabel(t('Address'))
      ->setTranslatable(FALSE)
      ->setCardinality(1)
      ->setDisplayOptions('view', array(
        'type' => 'generic_address',
        'weight' => -3,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'generic_address',
        'weight' => -3,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);
  
    $fields['payment_details'] = BaseFieldDefinition::create('donation_payment')
      ->setLabel(t('Payment Details'))
      ->setDescription(t('Payment Details'))
      ->setTranslatable(FALSE)
      ->setCardinality(1)
      ->setDisplayOptions('view', array(
        'type' => 'donation_payment',
        'weight' => -3,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'donation_payment',
        'weight' => -3,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);
  
    
    $fields['gift_aid'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Gift Aid'))
      ->setTranslatable(TRUE)
      ->setCardinality(1)
      ->setDefaultValue(0)
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
    
  
    $fields['response'] = BaseFieldDefinition::create('map')
      ->setLabel(t('Response Info'))
      ->setCardinality(1);
    
    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    
    return $fields;
  }

}
