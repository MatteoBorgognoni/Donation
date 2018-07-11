<?php

namespace Drupal\donation\Form;

use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\user\PrivateTempStoreFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Provides a donation deletion confirmation form.
 */
class DeleteMultiple extends ConfirmFormBase {

  /**
   * The array of donations to delete.
   *
   * @var string[][]
   */
  protected $donationInfo = [];

  /**
   * The tempstore factory.
   *
   * @var \Drupal\user\PrivateTempStoreFactory
   */
  protected $tempStoreFactory;

  /**
   * The donation storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $manager;

  /**
   * Constructs a DeleteMultiple form object.
   *
   * @param \Drupal\user\PrivateTempStoreFactory $temp_store_factory
   *   The tempstore factory.
   * @param \Drupal\Core\Entity\EntityManagerInterface $manager
   *   The entity manager.
   */
  public function __construct(PrivateTempStoreFactory $temp_store_factory, EntityManagerInterface $manager) {
    $this->tempStoreFactory = $temp_store_factory;
    $this->storage = $manager->getStorage('donation');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('user.private_tempstore'),
      $container->get('entity.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'donation_multiple_delete_confirm';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->formatPlural(count($this->donationInfo), 'Are you sure you want to delete this donation?', 'Are you sure you want to delete these donations?');
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('system.admin_content');
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $this->donationInfo = $this->tempStoreFactory->get('donation_multiple_delete_confirm')->get(\Drupal::currentUser()->id());
    if (empty($this->donationInfo)) {
      return new RedirectResponse($this->getCancelUrl()->setAbsolute()->toString());
    }
    /** @var \Drupal\donation\DonationInterface[] $donations */
    $donations = $this->storage->loadMultiple(array_keys($this->donationInfo));

    $items = [];
    foreach ($this->donationInfo as $id => $langcodes) {
      foreach ($langcodes as $langcode) {
        $donation = $donations[$id]->getTranslation($langcode);
        $key = $id . ':' . $langcode;
        $default_key = $id . ':' . $donation->getUntranslated()->language()->getId();

        // If we have a translated entity we build a nested list of translations
        // that will be deleted.
        $languages = $donation->getTranslationLanguages();
        if (count($languages) > 1 && $donation->isDefaultTranslation()) {
          $names = [];
          foreach ($languages as $translation_langcode => $language) {
            $names[] = $language->getName();
            unset($items[$id . ':' . $translation_langcode]);
          }
          $items[$default_key] = [
            'label' => [
              '#markup' => $this->t('@label (Original translation) - <em>The following content translations will be deleted:</em>', ['@label' => $donation->label()]),
            ],
            'deleted_translations' => [
              '#theme' => 'item_list',
              '#items' => $names,
            ],
          ];
        }
        elseif (!isset($items[$default_key])) {
          $items[$key] = $donation->label();
        }
      }
    }

    $form['donations'] = [
      '#theme' => 'item_list',
      '#items' => $items,
    ];
    $form = parent::buildForm($form, $form_state);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    if ($form_state->getValue('confirm') && !empty($this->donationInfo)) {
      $total_count = 0;
      $delete_donations = [];
      /** @var \Drupal\Core\Entity\ContentEntityInterface[][] $delete_translations */
      $delete_translations = [];
      /** @var \Drupal\donation\DonationInterface[] $donations */
      $donations = $this->storage->loadMultiple(array_keys($this->donationInfo));

      foreach ($this->donationInfo as $id => $langcodes) {
        foreach ($langcodes as $langcode) {
          $donation = $donations[$id]->getTranslation($langcode);
          if ($donation->isDefaultTranslation()) {
            $delete_donations[$id] = $donation;
            unset($delete_translations[$id]);
            $total_count += count($donation->getTranslationLanguages());
          }
          elseif (!isset($delete_donations[$id])) {
            $delete_translations[$id][] = $donation;
          }
        }
      }

      if ($delete_donations) {
        $this->storage->delete($delete_donations);
        $this->logger('content')->notice('Deleted @count donations.', ['@count' => count($delete_donations)]);
      }

      if ($delete_translations) {
        $count = 0;
        foreach ($delete_translations as $id => $translations) {
          $donation = $donations[$id]->getUntranslated();
          foreach ($translations as $translation) {
            $donation->removeTranslation($translation->language()->getId());
          }
          $donation->save();
          $count += count($translations);
        }
        if ($count) {
          $total_count += $count;
          $this->logger('content')->notice('Deleted @count content translations.', ['@count' => $count]);
        }
      }

      if ($total_count) {
        drupal_set_message($this->formatPlural($total_count, 'Deleted 1 donation.', 'Deleted @count donations.'));
      }

      $this->tempStoreFactory->get('donation_multiple_delete_confirm')->delete(\Drupal::currentUser()->id());
    }

    $form_state->setRedirect('system.admin_content');
  }

}
