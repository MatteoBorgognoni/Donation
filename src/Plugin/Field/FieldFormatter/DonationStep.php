<?php

namespace Drupal\donation\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\Plugin\Field\FieldFormatter\StringFormatter;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\SortArray;
use Drupal\donation\DonationWrapper;

/**
 * Plugin implementation of the 'donation_status' formatter.
 *
 * @FieldFormatter(
 *   id = "donation_step",
 *   label = @Translation("Donation step"),
 *   field_types = {
 *     "integer"
 *   }
 * )
 */
class DonationStep extends StringFormatter {
  
  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    $options = parent::defaultSettings();
    
    //$options['link_to_entity'] = FALSE;
    return $options;
  }
  
  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);
    
    return $form;
  }

  
  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    /** @var \Drupal\donation\Entity\DonationInterface $donation */
    $donation = $items->getEntity();
    $donation_type = $donation->bundle();
    /** @var \Drupal\donation\DonationManager $donationManager */
    $donationManager = \Drupal::service('donation.manager');
    
    $steps = $donationManager->getSteps($donation_type, TRUE);
  
    // count groups
    $count = count($steps);

    foreach ($items as $delta => $item) {
      $view_value = (int) $item->value;
      // If status value is 1 step text = completed
      if ($donation->get('status')->value) {
        $elements[$delta] = [
          '#markup' => 'Completed',
        ];
      }
      // If step value is <= of number of steps return step label
      elseif ($view_value <= $count) {
        $elements[$delta] = [
          '#markup' => $steps[$view_value],
        ];
      }
      else {
        $elements[$delta] = [
          '#markup' => '',
        ];
      }
      

    }
    return $elements;
  }
  

}
