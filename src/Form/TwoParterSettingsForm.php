<?php

declare(strict_types=1);

namespace Drupal\twoparter\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure Two-parter settings for this site.
 */
final class TwoParterSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'twoparter_two_parter_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['twoparter.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    // Autocomplete field for the twoparter_group taxonomy vocabulary.
    $term_id = $this->config('twoparter.settings')->get('which_group');
    if ($term_id && $term_storage = \Drupal::entityTypeManager()->getStorage('taxonomy_term')) {
      $term = $term_storage->load($term_id);
    }
    else {
      $term = NULL;
    }

    $form['which_group'] = [
      '#type' => 'entity_autocomplete',
      '#title' => $this->t('Which group'),
      '#target_type' => 'taxonomy_term',
      '#selection_settings' => [
        'target_bundles' => ['twoparter_group'],
      ],
      '#default_value' => $term,
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    // @todo Validate the form here.
    // Example:
    // @code
    //   if ($form_state->getValue('example') === 'wrong') {
    //     $form_state->setErrorByName(
    //       'message',
    //       $this->t('The value is not correct.'),
    //     );
    //   }
    // @endcode
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->config('twoparter.settings')
      ->set('which_group', $form_state->getValue('which_group'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
