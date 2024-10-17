<?php

declare(strict_types=1);

namespace Drupal\twoparter\Plugin\Block;

use Drupal;
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
final class DailyFeedBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    // Get daily feed.
    $dailyFeed = $this->getFeedContent('daily');
    $build['content'] = [
      '#markup' => $this->t('It works!'),
    ];
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account): AccessResult {
    // Get current user.
    $current_user = Drupal::currentUser();

    // @todo Evaluate the access condition here.
    return AccessResult::allowedIf($current_user->hasPermission('access content'));
  }

}
