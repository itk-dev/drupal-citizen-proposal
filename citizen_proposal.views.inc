<?php

/**
 * @file
 * Views for citizen proposal.
 */

/**
 * Implements hook_views_data().
 */
function citizen_proposal_views_data() {
  return [
    'citizen_proposal_support' => [
      'table' => [
        'group' => 'Citizen proposal',
        'base' => [
          'field' => 'id',
          'title' => 'Citizen proposal support',
          'help' => 'Citizen proposal support',
          'database' => 'default',
          'weight' => 10,
        ],
      ],

      'id' => [
        'title' => 'Id',
        'help' => 'Id',
        'field' => [
          'id' => 'numeric',
        ],
        'sort' => [
          'id' => 'standard',
        ],
        'filter' => [
          'id' => 'numeric',
        ],
        'argument' => [
          'id' => 'numeric',
        ],
      ],

      'node_id' => [
        'title' => 'Node id',
        'help' => 'Node id',
        'field' => [
          'id' => 'numeric',
        ],
        'sort' => [
          'id' => 'standard',
        ],
        'filter' => [
          'id' => 'numeric',
        ],
        'argument' => [
          'id' => 'numeric',
        ],
        'relationship' => [
          'base' => 'node_field_data',
          'id' => 'standard',
          'base field' => 'nid',
          'label' => 'Node id',
          'title' => 'Node id',
          'help' => 'Node id',
        ],
      ],

      'user_identifier' => [
        'title' => 'User identifier',
        'help' => 'User identifier',
        'field' => [
          'id' => 'standard',
        ],
        'sort' => [
          'id' => 'standard',
        ],
        'filter' => [
          'id' => 'string',
        ],
        'argument' => [
          'id' => 'string',
        ],
      ],

      'user_name' => [
        'title' => 'User name',
        'help' => 'User name',
        'field' => [
          'id' => 'standard',
        ],
        'sort' => [
          'id' => 'standard',
        ],
        'filter' => [
          'id' => 'string',
        ],
        'argument' => [
          'id' => 'string',
        ],
      ],

      'user_email' => [
        'title' =>
        'User email',
        'help' => 'User email',
        'field' => [
          'id' => 'standard',
        ],
        'sort' => [
          'id' => 'standard',
        ],
        'filter' => [
          'id' => 'string',
        ],
        'argument' => [
          'id' => 'string',
        ],
      ],

      'created' => [
        'title' =>
        'Created',
        'help' => 'Created',
        'field' => [
          'id' => 'numeric',
        ],
        'sort' => [
          'id' => 'standard',
        ],
        'filter' => [
          'id' => 'numeric',
        ],
        'argument' => [
          'id' => 'numeric',
        ],
      ],

      'allow_email' => [
        'title' =>
        'Allow email',
        'help' =>
        'Allow email',
        'field' => [
          'id' => 'numeric',
        ],
        'sort' => [
          'id' => 'standard',
        ],
        'filter' => [
          'id' => 'numeric',
        ],
        'argument' => [
          'id' => 'numeric',
        ],
      ],
    ],

    'node' => [
      'citizen_proposal_support' => [
        'title' => 'Citizen proposal support',
        'help' => 'citizen_proposal_support',
        'relationship' => [
          'id' => 'entity_reverse',
          'field_name' => 'citizen_proposal_support',
          'field table' => 'citizen_proposal_support',
          'field field' => 'node_id',
          'base' => 'citizen_proposal_support',
          'base field' => 'node_id',
          'label' => 'Citizen proposal support',
        ],
      ],
    ],

  ];
}
