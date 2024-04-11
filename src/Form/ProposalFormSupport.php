<?php

namespace Drupal\citizen_proposal\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\node\NodeInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Form for supporting proposal.
 */
final class ProposalFormSupport extends ProposalFormBase {
  public const SURVEY_KEY = 'support_proposal_survey';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'proposal_support_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getAuthenticateMessage(): string|TranslatableMarkup {
    return $this->getAdminFormStateValue(
      ['authenticate_support_message', 'value'],
      $this->t('You have to authenticate to support a proposal')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, NodeInterface $node = NULL): RedirectResponse|array {
    // Pass the node to the submit handler.
    $form['#node'] = $node;

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  protected function buildProposalForm(array $form, FormStateInterface $formState): array|RedirectResponse {
    $node = $form['#node'];
    assert($node instanceof NodeInterface);

    if ($this->isAuthenticatedAsCitizen()) {
      $supportedAt = $this->helper->getUserSupportedAt($this->getUserUuid(), $node);
      if (NULL !== $supportedAt) {
        $form['message'] = [
          '#markup' => $this->t('You already supported this proposal on @support_date. You can only support a proposal once.',
            [
              '@support_date' => $supportedAt->format('d/m/Y'),
            ]),
        ];
        return $form;
      }
    }
    elseif ($this->isAuthenticatedAsEditor()) {
      $form['message'] = [
        '#theme' => 'status_messages',
        '#message_list' => [
          'warning' => [$this->t("You're supporting @label on behalf of a citizen", [
            '@label' => $node->label(),
          ]),
          ],
        ],
      ];
    }
    else {
      return [];
    }

    $defaltValues = $this->getDefaultFormValues();

    $form['support_intro'] = [
      '#type' => 'processed_text',
      '#format' => $this->getAdminFormStateValue(['support_intro', 'format'], 'filtered_html'),
      '#text' => $this->getAdminFormStateValue(['support_intro', 'value'], ''),

    ];

    $form['name'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this
        ->t('Name'),
      '#default_value' => $defaltValues['name'],
      '#attributes' => ['readonly' => !$this->isAuthenticatedAsEditor()],
      '#description' => $this->getAdminFormStateValue('support_name_help'),
      '#description_display' => 'before',
    ];

    $form['email'] = [
      '#type' => 'email',
      '#title' => $this
        ->t('Email'),
      '#default_value' => $defaltValues['email'],
      '#description' => $this->getAdminFormStateValue('support_email_help'),
      '#description_display' => 'before',
    ];

    $form['allow_email'] = [
      '#type' => 'checkbox',
      '#title' => $this
        ->t('Allow email'),
      '#default_value' => $defaltValues['support_allow_email_help'] ?? FALSE,
      '#description' => $this->getAdminFormStateValue('support_allow_email_help'),
      '#states' => [
        'visible' => [
          ':input[name="email"]' => ['filled' => TRUE],
        ],
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
      '#value' => $this->t('Support proposal'),
      '#button_type' => 'primary',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  protected function getDefaultFormValues(): array {
    return [
      'name' => $this->getUserData('name'),
      'email' => $this->getUserData('email'),
      'phone' => $this->getUserData('phone'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    /** @var \Drupal\node\NodeInterface $node */
    $node = $form['#node'];

    if (!$this->isAuthenticated()) {
      $form_state
        ->setRedirect('citizen_proposal.support', ['node' => $node->id()]);
      return;
    }

    try {
      $this->helper->saveSupport(
        $this->getUserUuid(),
        $node,
        [
          'user_name' => $form_state->getValue('name'),
          'user_email' => $form_state->getValue('email'),
          'allow_email' => $form_state->getValue('allow_email'),
        ],
      );
      $this->messenger()->addStatus($this->getAdminFormStateValue('support_submission_text', $this->t('Thank you for your support.')));

      $this->setSurveyResponse($form_state);
      $this->saveSurveyResponse($node);
    }
    catch (\Exception $e) {
      $this->messenger()->addError($this->t('Something went wrong. Your support was not registered.'));
    }

    $form_state->setRedirectUrl(
      $this->deauthenticateUser(
        $this->getAdminFormStateValueUrl('support_goto_url', NULL, $node->toUrl())
      )
    );
  }

}
