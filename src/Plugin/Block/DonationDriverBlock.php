<?php

namespace Drupal\donation\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Provides a 'DonationDriverBlock' block.
 *
 * @Block(
 *  id = "donation_driver_block",
 *  admin_label = @Translation("Donation driver"),
 * )
 */
class DonationDriverBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
        'campaign_driver_text' => $this->t('Donate'),
        'campaign_driver_url'  => '/donate',
      ] + parent::defaultConfiguration();

  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {

    $form['campaign_driver_text'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Button text'),
      '#description'   => $this->t('Default: Donate'),
      '#default_value' => $this->configuration['campaign_driver_text'],
      '#weight'        => '5',
    ];

    $form['campaign_driver_url'] = [
      '#type'          => 'linkit',
      '#title'         => $this->t('Campaign URL'),
      '#default_value' => $this->configuration['campaign_driver_url'],
      '#weight'        => '6',
      '#required'      => TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['campaign_driver_text'] = $form_state->getValue('campaign_driver_text');
    $this->configuration['campaign_driver_url'] = $form_state->getValue('campaign_driver_url');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];

    // Retrieve uri
    $uri = $this->configuration['campaign_driver_url'];
    if (strpos($uri, 'http') === 0) {
      $uri = 'external:' . $uri;
    }
    else {
      $uri = 'internal:' . $uri;
    }

    $build['donation_driver_block_campaign_url'] = [
      '#title'      => $this->configuration['campaign_driver_text'],
      '#type'       => 'link',
      '#url'        => Url::fromUri($uri),
      '#attributes' => [
        'class' => ['button', 'button--donate']
      ]
    ];

    return $build;
  }

}
