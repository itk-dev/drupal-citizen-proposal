<?php

namespace Drupal\citizen_proposal\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\node\Entity\Node;

/**
 * Form for adding proposal.
 */
final class ProposalFormAdd extends ProposalFormBase {
  public const SURVEY_KEY = 'create_proposal_survey';

  public const ADD_FORM_TITLE_MAXLENGTH = 80;
  public const ADD_FORM_PROPOSAL_MAXLENGTH = 2000;
  public const ADD_FORM_REMARKS_MAXLENGTH = 10000;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'proposal_add_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getAuthenticateMessage(): string|TranslatableMarkup {
    return $this->getAdminFormStateValue(
      ['authenticate_message', 'value'],
      $this->t('You have to authenticate to add a proposal')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildProposalForm(array $form, FormStateInterface $formState): array {
    $defaltValues = $this->getDefaultFormValues();

    $form['author_intro_container'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['mt-5']],
    ];

    $form['author_intro_container']['author_intro'] = [
      '#type' => 'processed_text',
      '#format' => $this->getAdminFormStateValue(['author_intro', 'format'], 'filtered_html'),
      '#text' => $this->getAdminFormStateValue(['author_intro', 'value'], ''),
    ];

    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this
        ->t('Name'),
      '#default_value' => $defaltValues['name'],
      '#attributes' => ['readonly' => TRUE, 'class' => ['mb-3']],
      '#description' => $this->getAdminFormStateValue('name_help'),
      '#description_display' => 'before',
    ];

    if ($this->isAuthenticatedAsEditor()) {
      $form['name']['#required'] = TRUE;
      unset($form['name']['#attributes']['readonly']);
    }

    $form['phone'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this
        ->t('Phone'),
      '#default_value' => $defaltValues['phone'],
      '#attributes' => ['class' => ['mb-3']],
      '#description' => $this->getAdminFormStateValue('phone_help'),
      '#description_display' => 'before',
    ];

    $form['email'] = [
      '#type' => 'email',
      '#required' => TRUE,
      '#title' => $this
        ->t('Email'),
      '#default_value' => $defaltValues['email'],
      '#description' => $this->getAdminFormStateValue('email_help'),
      '#description_display' => 'before',
    ];

    $form['email_display'] = [
      '#type' => 'checkbox',
      '#title' => $this
        ->t('Display email'),
      '#default_value' => $defaltValues['email_display'] ?? TRUE,
      '#description' => $this->getAdminFormStateValue('email_display_help'),
    ];

    $form['allow_email'] = [
      '#type' => 'checkbox',
      '#title' => $this
        ->t('Allow email'),
      '#default_value' => $defaltValues['allow_email'] ?? FALSE,
      '#description' => $this->getAdminFormStateValue('allow_email_help'),
    ];

    $form['proposal_intro_container'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['mt-5']],
    ];

    $form['proposal_intro_container']['proposal_intro'] = [
      '#type' => 'processed_text',
      '#format' => $this->getAdminFormStateValue(['proposal_intro', 'format'], 'filtered_html'),
      '#text' => $this->getAdminFormStateValue(['proposal_intro', 'value'], ''),
    ];

    $form['title'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this
        ->t('Title'),
      '#description' => $this->getAdminFormStateValue('title_help'),
      '#description_display' => 'before',
      '#default_value' => $defaltValues['title'],
      '#maxlength_js' => TRUE,
      '#attributes' => [
        'data-maxlength' => $this->getMaxLength('characters_title'),
        'maxlength_js_label' => $this->t('@remaining characters left.'),
      ],
    ];

    $form['proposal'] = [
      '#type' => 'text_format',
      '#format' => self::CONTENT_TEXT_FORMAT,
      '#allowed_formats' => [self::CONTENT_TEXT_FORMAT],
      '#required' => TRUE,
      '#rows' => 15,
      '#title' => $this
        ->t('Proposal'),
      '#description' => $this->getAdminFormStateValue('proposal_help'),
      '#description_display' => 'before',
      '#default_value' => $defaltValues['proposal'] ?? '',
      '#maxlength_js' => TRUE,
      '#attributes' => [
        'data-maxlength' => $this->getMaxLength('characters_proposal'),
        'maxlength_js_label' => $this->t('@remaining characters left.'),
      ],
    ];

    $form['remarks'] = [
      '#type' => 'text_format',
      '#format' => self::CONTENT_TEXT_FORMAT,
      '#allowed_formats' => [self::CONTENT_TEXT_FORMAT],
      '#required' => TRUE,
      '#rows' => 15,
      '#title' => $this
        ->t('Remarks'),
      '#description' => $this->getAdminFormStateValue('remarks_help'),
      '#description_display' => 'before',
      '#default_value' => $defaltValues['remarks'] ?? '',
      '#maxlength_js' => TRUE,
      '#attributes' => [
        'data-maxlength' => $this->getMaxLength('characters_remarks'),
        'maxlength_js_label' => $this->t('@remaining characters left.'),
      ],
    ];

    $this->buildSurveyForm($form);

    $form['consent'] = [
      '#type' => 'checkbox',
      '#title' => $this
        ->t('Personal data storage consent'),
      '#required' => TRUE,
      '#default_value' => FALSE,
      '#description' => $this->getAdminFormStateValue('consent_help'),
    ];

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->helper->hasDraftProposal()
        ? $this->t('Update proposal')
        : $this->t('Create proposal'),
      '#button_type' => 'primary',
    ];

    $form['#after_build'][] = $this->afterBuildForm(...);

    return $form;
  }

  /**
   * Form after build handler.
   *
   * @param array $form
   *   The form.
   * @param \Drupal\Core\Form\FormStateInterface $formState
   *   The form state.
   *
   * @return array
   *   The form.
   */
  private function afterBuildForm(array $form, FormStateInterface $formState) {
    // Hide text format info
    // (lifted from _allowed_formats_remove_textarea_help()).
    foreach ($form as &$element) {
      if (isset($element['format'])) {
        unset(
          $element['format']['help'],
          $element['format']['guidelines']
        );
      }
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    if (strlen($form_state->getValue('title')) > $this->getMaxLength('characters_title')) {
      $form_state->setErrorByName('title', $this->t('Too many characters used.'));
    }

    $getTextValue = function (string $key) use ($form_state) {
      $value = $form_state->getValue($key);
      if (isset($value['value'])) {
        $value = $value['value'];
      }

      return strip_tags((string) $value);
    };

    if (strlen($getTextValue('proposal')) > $this->getMaxLength('characters_proposal')) {
      $form_state->setErrorByName('proposal', $this->t('Too many characters used.'));
    }

    if (strlen($getTextValue('remarks')) > $this->getMaxLength('characters_remarks')) {
      $form_state->setErrorByName('remarks', $this->t('Too many characters used.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $formState): void {
    $entity = Node::create([
      'type' => 'citizen_proposal',
      'title' => $formState->getValue('title'),
      'field_author_uuid' => $this->getUserUuid(),
      'field_author_name' => $formState->getValue('name'),
      'field_author_phone' => $formState->getValue('phone'),
      'field_author_email' => $formState->getValue('email'),
      'field_author_email_display' => $formState->getValue('email_display'),
      'field_proposal' => $formState->getValue('proposal'),
      'field_remarks' => $formState->getValue('remarks'),
      'field_author_allow_email' => $formState->getValue('allow_email'),
    ]);
    $this->helper->setDraftProposal($entity);
    $formState
      ->setRedirect('citizen_proposal.citizen_proposal.proposal_approve');

    $this->setSurveyResponse($formState);
  }

  /**
   * Get a number of characters from admin form or constant.
   *
   * @return int
   *   The calculated number of characters.
   */
  private function getMaxLength($adminFormElement): int {
    $value = (int) $this->getAdminFormStateValue($adminFormElement);
    if ($value > 0) {
      return $value;
    }

    return match ($adminFormElement) {
      'characters_title' => self::ADD_FORM_TITLE_MAXLENGTH,
      'characters_proposal' => self::ADD_FORM_PROPOSAL_MAXLENGTH,
      'characters_remarks' => self::ADD_FORM_REMARKS_MAXLENGTH,
      default => 0,
    };
  }

}
