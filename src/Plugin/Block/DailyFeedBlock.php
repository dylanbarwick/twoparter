<?php

declare(strict_types=1);

namespace Drupal\twoparter\Plugin\Block;

use Drupal\Core\Drupal;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\twoparter\Controller\TwoParterFeedController;

/**
 * Provides a daily feed block block.
 *
 * @Block(
 *   id = "twoparter_daily_feed_block",
 *   admin_label = @Translation("Daily feed block"),
 *   category = @Translation("Custom"),
 * )
 */
class DailyFeedBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    // Get defaults from twoparter.settings.
    $config = \Drupal::config('twoparter.settings');

    $taxonomy_id = $config->get('which_group');
    // Get daily feed.
    $dailyFeedContent = '';
    // Get feed from TwoParterFeedController
    $controller = new TwoParterFeedController();
    $response = $controller->dailyfeed($taxonomy_id);
    $headers = $response->headers->get('Content-Type');
    $gotContent = $response->getContent();
    switch ($headers) {
      case 'application/json':
        $dailyFeed = json_decode($gotContent, TRUE);
        break;
      case 'application/xml':
        $dailyFeed = simplexml_load_string($gotContent);
        break;
      default:
        $dailyFeed = [];
        break;
    }
    if (isset($dailyFeed['parts'])) {
      foreach ($dailyFeed['parts'] as $part) {
        $dailyFeedContent .= '<h4>' . $part['snippet'] . '</h4>';
        $dailyFeedContent .= '<div>' . $part['supporting'] . '</div>';
      }
    }

    $build['content'] = [
      '#markup' => $dailyFeedContent,
    ];
    $build['#cache']['max-age'] = 0;
    $build['#attributes']['class'][] = 'daily-feed-block';
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account): AccessResult {
    // Get current user.
    $current_user = \Drupal::currentUser();

    // @todo Evaluate the access condition here.
    return AccessResult::allowedIf($current_user->hasPermission('access content'));
  }

}
