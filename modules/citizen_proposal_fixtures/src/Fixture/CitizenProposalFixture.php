<?php

namespace Drupal\citizen_proposal_fixtures\Fixture;

use Drupal\citizen_proposal\Form\ProposalFormBase;
use Drupal\citizen_proposal\Helper\Helper;
use Drupal\citizen_proposal\Helper\MailHelper;
use Drupal\content_fixtures\Fixture\AbstractFixture;
use Drupal\content_fixtures\Fixture\FixtureGroupInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * Page fixture.
 *
 * @package Drupal\citizen_proposal_fixtures\Fixture
 */
class CitizenProposalFixture extends AbstractFixture implements FixtureGroupInterface {
  private const DATE_TIME_FORMAT = 'Y-m-d\TH:i:s';

  /**
   * Constructor.
   */
  public function __construct(
    readonly private Helper $helper,
    EventDispatcherInterface $eventDispatcher,
    MailHelper $mailHelper
  ) {
    // Prevent sending notification emails.
    $eventDispatcher->removeSubscriber($mailHelper);
  }

  /**
   * {@inheritdoc}
   */
  public function load() {
    // Citizen proposal nodes.
    $entity = Node::create([
      'type' => 'citizen_proposal',
      'title' => 'Borgerforslag nummer 1',
      'status' => NodeInterface::PUBLISHED,
      'field_author_uuid' => '1111',
      'field_author_name' => 'Anders And',
      'field_author_email' => 'aand@eksempel.dk',
      'field_content_state' => 'upcoming',
      'field_proposal' => [
        'value' => <<<'VALUE'

VALUE,
        'format' => ProposalFormBase::CONTENT_TEXT_FORMAT,
      ],
      'field_remarks' => [
        'value' => <<<'VALUE'

VALUE,
        'format' => ProposalFormBase::CONTENT_TEXT_FORMAT,
      ],
    ]);
    $entity->save();

    // field_vote_start and field_vote_end are set on first publish, so we set
    // it after saving once.
    $entity->field_vote_start->setValue((new DrupalDateTime('tomorrow'))->format(self::DATE_TIME_FORMAT));
    $entity->field_vote_end->setValue((new DrupalDateTime('tomorrow + 3 months'))->format(self::DATE_TIME_FORMAT));
    $entity->save();

    $this->addReference('node:citizen_proposal:Proposal1', $entity);

    // Add some support.
    for ($i = 0; $i < 87; $i++) {
      $this->helper->saveSupport(uniqid('', TRUE), $entity, ['user_name' => self::class]);
    }

    $entity = Node::create([
      'type' => 'citizen_proposal',
      'title' => 'Borgerforslag nummer 2',
      'status' => NodeInterface::PUBLISHED,
      'field_author_uuid' => '2222',
      'field_author_name' => 'Fedtmule',
      'field_author_email' => 'fedtmule@eksempel.dk',
      'field_content_state' => 'finished',
      'field_proposal' => [
        'value' => <<<'VALUE'

VALUE,
        'format' => ProposalFormBase::CONTENT_TEXT_FORMAT,
      ],
      'field_remarks' => [
        'value' => <<<'VALUE'

VALUE,
        'format' => ProposalFormBase::CONTENT_TEXT_FORMAT,
      ],
    ]);
    $entity->save();

    $entity->field_vote_start->setValue((new DrupalDateTime('yesterday - 3 months'))->format(self::DATE_TIME_FORMAT));
    $entity->field_vote_end->setValue((new DrupalDateTime('yesterday'))->format(self::DATE_TIME_FORMAT));

    $entity->save();

    $this->addReference('node:citizen_proposal:Proposal2', $entity);

    // Add some support.
    for ($i = 0; $i < 3; $i++) {
      $this->helper->saveSupport(uniqid('', TRUE), $entity, ['user_name' => self::class]);
    }

    $entity = Node::create([
      'type' => 'citizen_proposal',
      'title' => 'Borgerforslag nummer 3',
      'status' => NodeInterface::NOT_PUBLISHED,
      'field_author_uuid' => '3333',
      'field_author_name' => 'Hexia De Trick',
      'field_author_email' => 'givmiglykkemønten@eksempel.dk',
      'field_content_state' => 'active',
      'field_proposal' => [
        'value' => <<<'VALUE'

VALUE,
        'format' => ProposalFormBase::CONTENT_TEXT_FORMAT,
      ],
      'field_remarks' => [
        'value' => <<<'VALUE'

VALUE,
        'format' => ProposalFormBase::CONTENT_TEXT_FORMAT,
      ],
    ]);
    $entity->save();
    $this->addReference('node:citizen_proposal:Proposal3', $entity);

    // Set admin values.
    $data = Yaml::parseFile(__DIR__ . '/CitizenProposalFixture/citizen_proposal_admin_form_values.yaml');
    if (isset($data['citizen_proposal_admin_form_values'])) {
      $values = $data['citizen_proposal_admin_form_values'];

      $this->helper->setAdminValues($values);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getGroups() {
    return ['citizen_proposal'];
  }

}
