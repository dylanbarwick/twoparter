<?php

declare(strict_types=1);

namespace Drupal\twoparter\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Returns responses for Two-parter routes.
 */
final class TwoParterFeedController extends ControllerBase {
  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  private EntityTypeManagerInterface $entityTypeManager;

  /**
   * Constructs a new TwoParterFeedController object.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * Builds the response.
   */
  public function __invoke(): array {

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('It works!'),
    ];

    return $build;
  }

  /**
   * Returns twoparter content as JSON.
   *
   * @param string $whichFeedGroup
   *   The taxonomy ID of the group in question.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   */
  public function dailyfeed($whichFeedGroup): JsonResponse {
    // Fetch the twoparter content.
    $fetchedContent = $this->getFeedContent($whichFeedGroup);

    return new JsonResponse($fetchedContent);
  }

  /**
   * Retrieves the relevant twoparter content and formulates it as JSON.
   */
  protected function getFeedContent($whichFeedGroup): array {
    // Fetch the twoparter content.
    // It should look like this...
    $example_content = [
      'parts' => [
        [
          'snippet' => 'Part 1',
          'supporting' => 'This is the first part of the feed content.',
        ],
        // Optional second part.
        [
          'snippet' => 'Part 2',
          'supporting' => 'This is the second part of the feed content.',
        ],
      ],
    ];

    // Fetch the most recent twoparter node.
    $query = $this->entityTypeManager->getStorage('node')->getQuery();
    // Get timestamp for today at 23:59:59.
    $endOfDay = strtotime('today 23:59:59');
    $query->condition('type', 'twoparter')
      ->condition('field_start_time_1', $endOfDay, '<=')
      ->condition('field_which_group', $whichFeedGroup)
      ->sort('field_start_time_1', 'DESC')
      ->range(0, 1);
    $nids = $query->execute();
    $node = Node::load(array_pop($nids));

    // Declare current time.
    $current_time = time();



    // If startTime2 is set and current time is later than startTime2 then return startTime2 content.
    $startTime2 = $node->get('field_start_time_2')->value;
    if ($startTime2 && $current_time >= $startTime2) {
      $content = [
        'parts' => [
          [
            'snippet' => $node->get('field_snippet_2')->value,
            'supporting' => $node->get('field_supporting_text_2')->value,
          ],
        ],
      ];
    }
    else {
      $content = [
        'parts' => [
          [
            'snippet' => $node->get('field_snippet_1')->value,
            'supporting' => $node->get('field_supporting_1')->value,
          ],
        ],
      ];
    }

    return $content;
  }

}
