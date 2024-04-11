<?php

namespace Drupal\citizen_proposal\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\citizen_proposal\Helper\Helper;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides citizen proposal support counter.
 *
 * @Block(
 *   id = "citizen_proposal_support_counter",
 *   admin_label = @Translation("Citizen proposal support counter"),
 * )
 */
final class CitizenProposalSupportCounter extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Constructor for the proposal support counter.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    readonly private RouteMatchInterface $routeMatch,
    readonly protected Helper $helper,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_route_match'),
      $container->get(Helper::class),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $node = $this->routeMatch->getParameter('node');
    if (!$node) {
      return [];
    }
    $supportCount = $this->helper->getProposalSupportCount($node->id());
    $supportRequired = $this->helper->getProposalSupportRequired();
    $supportPercentage = $this->helper->calculateSupportPercentage($supportCount);

    return [
      '#theme' => 'citizen_proposal_support_counter',
      '#data' => [
        'supportCount' => $supportCount,
        'supportRequired' => $supportRequired,
        'supportPercentage' => $supportPercentage,
      ],
      '#cache' => [
        'contexts' => [],
        'tags' => [],
      ],
    ];
  }

}
