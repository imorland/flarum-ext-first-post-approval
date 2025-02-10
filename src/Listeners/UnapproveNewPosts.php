<?php

namespace ClarkWinkelmann\FirstPostApproval\Listeners;

use ClarkWinkelmann\FirstPostApproval\Repository\FirstPostApprovalRepository;
use Flarum\Post\Event\Saving;
use Flarum\Settings\SettingsRepositoryInterface;

class UnapproveNewPosts
{
    protected $settings;
    protected $firstPosts;

    public function __construct(SettingsRepositoryInterface $settings, FirstPostApprovalRepository $firstPosts)
    {
        $this->settings = $settings;
        $this->firstPosts = $firstPosts;
    }

    public function handle(Saving $event)
    {
        $post = $event->post;

        if ($post->exists || $event->actor->can('firstPostWithoutApproval', $post->discussion)) {
            return;
        }

        $discussionCount = $this->firstPosts->requiredDiscussionCount();

        if ($post->discussion->first_post_id === null && $discussionCount) {
            // If this is a new discussion and if a rule has been defined for new discussions
            if ($event->actor->first_discussion_approval_count >= $discussionCount) {
                return;
            }
        } else {
            // If this is a reply, or if there's no rule defined for new discussions
            if (($event->actor->first_discussion_approval_count + $event->actor->first_post_approval_count) >= $this->firstPosts->requiredPostCount()) {
                return;
            }
        }

        $post->is_approved = false;

        $this->firstPosts->flagPost($post);
    }
}
