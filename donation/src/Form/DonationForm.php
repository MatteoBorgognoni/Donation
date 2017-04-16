<?php

namespace Drupal\donation\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for Donation edit forms.
 *
 * @ingroup donation
 */
class DonationForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    /* @var $entity \Drupal\donation\Entity\Donation */
    $form = parent::buildForm($form, $form_state);
    
    $entity = $this->entity;

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = &$this->entity;

    $status = parent::save($form, $form_state);

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Donation.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Donation.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.donation.canonical', ['donation' => $entity->id()]);
  }
  
  
  /**
   * Returns an array of supported actions for the current entity form.
   *
   */
  protected function actions(array $form, FormStateInterface $form_state) {
    // @todo Consider renaming the action key from submit to save. The impacts
    //   are hard to predict. For example, see
    //   \Drupal\language\Element\LanguageConfiguration::processLanguageConfiguration().
    $actions['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Complete Donation'),
      '#submit' => array('::submitForm', '::save'),
    );
    
    if (!$this->entity->isNew() && $this->entity->hasLinkTemplate('delete-form')) {
      $route_info = $this->entity->urlInfo('delete-form');
      if ($this->getRequest()->query->has('destination')) {
        $query = $route_info->getOption('query');
        $query['destination'] = $this->getRequest()->query->get('destination');
        $route_info->setOption('query', $query);
      }
      $actions['delete'] = array(
        '#type' => 'link',
        '#title' => $this->t('Delete'),
        '#access' => $this->entity->access('delete'),
        '#attributes' => array(
          'class' => array('button', 'button--danger'),
        ),
      );
      $actions['delete']['#url'] = $route_info;
    }
    
    return $actions;
  }
  
  

}
