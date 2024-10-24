<?php

declare(strict_types=1);

namespace Drupal\twoparter\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use SimpleXMLElement;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Returns responses for Two-parter routes.
 */
final class TwoParterFeedController extends ControllerBase {

  /**
   * Returns twoparter content as JSON or XML.
   *
   * @param string $whichFeedGroup
   *   The taxonomy ID of the group in question.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function dailyfeed($whichFeedGroup, $whichFormat = 'json'): Response {
   // Fetch the twoparter content as an array.
   $fetchedContent = $this->getFeedContent($whichFeedGroup);

   switch ($whichFormat) {
    case 'xml':
      $xml = new SimpleXMLElement('<root/>');
      array_walk_recursive($fetchedContent, function($value, $key) use ($xml) {
          $xml->addChild($key, $value);
      });
      $xml = $xml->asXML();
      return new Response($xml, 200, ['Content-Type' => 'application/xml']);
    case 'json':
      return new JsonResponse($fetchedContent);
    default:
      return [];
   }

  }

  /**
   * Returns twoparter content as JSON - but restricted somehow.
   *
   * @param string $whichFeedGroup
   *   The taxonomy ID of the group in question.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   */
  public function dailyfeedRestricted($whichFeedGroup): JsonResponse {
    // Fetch the twoparter content.
    $fetchedContent = $this->getFeedContent($whichFeedGroup);

    return new JsonResponse($fetchedContent);
  }

  /**
   * Retrieves the relevant twoparter content and formulates it as JSON.
   */
  protected function getFeedContent($whichFeedGroup): array {
    // Fetch the most recent twoparter node.
    $query = $this->entityTypeManager()->getStorage('node')->getQuery();
    // Get timestamp for today at 23:59:59.
    $endOfDay = strtotime('today 23:59:59');
    $query->condition('type', 'twoparter')
      ->condition('field_start_time_1', $endOfDay, '<=')
      ->condition('field_which_group', $whichFeedGroup)
      ->sort('field_start_time_1', 'DESC')
      ->accessCheck(TRUE)
      ->range(0, 1);
    $nids = $query->execute();
    if (!empty($nids)) {
      $node = Node::load(array_pop($nids));
    }
    else {
      return [];
    }

    // Declare current time.
    $current_time = time();



    // If startTime2 is set and current time is later than startTime2 then return startTime2 content.
    $startTime2 = $node->get('field_start_time_2')->value;
    if ($startTime2 && $current_time >= $startTime2) {
      $content = [
        'parts' => [
          [
            'whichpart' => '2',
            'starttime' => $node->get('field_start_time_2')->value,
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
            'whichpart' => '1',
            'starttime' => $node->get('field_start_time_1')->value,
            'snippet' => $node->get('field_snippet_1')->value,
            'supporting' => $node->get('field_supporting_text_1')->value,
          ],
        ],
      ];
    }

    return $content;
  }

}
