<?php

namespace Drupal\donation\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Donation entities.
 */
class DonationViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Fields
    $data['donation']['formatted_amount'] = array(
      'title' => t('Formatted Amount'),
      'field' => array(
        'title' => t('Formatted Amount'),
        'help' => t('Displays the donation formatted amount.'),
        'id' => 'donation_formatted_amount',
      ),
      'real field' => 'amount',
    );

    $data['donation']['title'] = array(
      'title' => t('Title'),
      'field' => array(
        'title' => t('Donor title'),
        'help' => t('Displays the title of the donor.'),
        'id' => 'donation_customer_data',
      ),
    );

    $data['donation']['first_name'] = array(
      'title' => t('First Name'),
      'field' => array(
        'title' => t('First Name'),
        'help' => t('Displays the first name of the donor.'),
        'id' => 'donation_customer_data',
      ),
    );

    $data['donation']['last_name'] = array(
      'title' => t('Last Name'),
      'field' => array(
        'title' => t('Last Name'),
        'help' => t('Displays the last name of the donor.'),
        'id' => 'donation_customer_data',
      ),
    );

    $data['donation']['full_name'] = array(
      'title' => t('Full Name'),
      'field' => array(
        'title' => t('Full Name'),
        'help' => t('Displays the full name of the donor.'),
        'id' => 'donation_customer_data',
      ),
    );

    $data['donation']['email'] = array(
      'title' => t('Email'),
      'field' => array(
        'title' => t('Email'),
        'help' => t('Displays the email of the donor.'),
        'id' => 'donation_customer_data',
      ),
    );

    $data['donation']['phone'] = array(
      'title' => t('Telephone'),
      'field' => array(
        'title' => t('Telephone'),
        'help' => t('Displays the telephone number of the donor.'),
        'id' => 'donation_customer_data',
      ),
    );

    $data['donation']['address_line_1'] = array(
      'title' => t('Address line 1'),
      'field' => array(
        'title' => t('Address line 1'),
        'help' => t('Displays the Address line 1 of the donor.'),
        'id' => 'donation_address_data',
      ),
    );

    $data['donation']['address_line_2'] = array(
      'title' => t('Address line 2'),
      'field' => array(
        'title' => t('Address line 2'),
        'help' => t('Displays the Address line 2 of the donor.'),
        'id' => 'donation_address_data',
      ),
    );

    $data['donation']['address_line_3'] = array(
      'title' => t('Address line 3'),
      'field' => array(
        'title' => t('Address line 3'),
        'help' => t('Displays the Address line 3 of the donor.'),
        'id' => 'donation_address_data',
      ),
    );

    $data['donation']['city'] = array(
      'title' => t('City'),
      'field' => array(
        'title' => t('City'),
        'help' => t('Displays the city of the donor.'),
        'id' => 'donation_address_data',
      ),
    );

    $data['donation']['post_code'] = array(
      'title' => t('Post code'),
      'field' => array(
        'title' => t('Post code'),
        'help' => t('Displays the post code of the donor.'),
        'id' => 'donation_address_data',
      ),
    );

    $data['donation']['country'] = array(
      'title' => t('Country'),
      'field' => array(
        'title' => t('Country'),
        'help' => t('Displays the country of the donor.'),
        'id' => 'donation_address_data',
      ),
    );

    $data['donation']['customer'] = array(
      'title' => t('Customer - Customer Data'),
      'field' => array(
        'title' => t('Customer Data'),
        'help' => t('Displays customer data'),
        'id' => 'donation_customer',
      ),
    );
  
    $data['donation']['response'] = array(
      'title' => t('Response Data'),
      'field' => array(
        'title' => t('Response Data'),
        'help' => t('Displays response data'),
        'id' => 'donation_response',
      ),
    );
  
    $data['donation']['gift_aid_date'] = array(
      'title' => t('Gift Aid date'),
      'field' => array(
        'title' => t('Gift Aid date'),
        'help' => t('Displays Gift Aid date if available'),
        'id' => 'donation_gift_aid_date',
      ),
    );
  
    $data['donation']['donation_bulk_form'] = [
      'title' => $this->t('Donation operations bulk form'),
      'help' => $this->t('Add a form element that lets you run operations on multiple donations.'),
      'field' => [
        'id' => 'donation_bulk_form',
      ],
    ];
  
    // Filters
    $data['donation_field_data']['date_filter'] = [
      'title' => $this->t('Donation date'),
      'help' => $this->t('Filter by date.'),
      'filter' => [
        'id' => 'donation_date',
        'field' => 'changed',
      ],
      'entity_field' => 'changed',
    ];
  
    $data['donation_field_data']['origin_filter'] = [
      'title' => $this->t('Donation origin'),
      'help' => $this->t('Filter by origin.'),
      'filter' => [
        'id' => 'donation_origin',
        'field' => 'origin',
        'entity_type' => 'node',
        'donation_target_entity_type_id' => 'node',
        'allow empty' => TRUE,
        'additional fields' => [],
      ],
    ];
  
    $data['donation_field_data']['status']['filter']['id'] = 'donation_status';
    
   
    //ksm($data);
    
    return $data;
  }

}