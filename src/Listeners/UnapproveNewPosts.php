<?php

namespace ClarkWinkelmann\FirstPostApproval\Listeners;

use ClarkWinkelmann\FirstPostApproval\Repository\FirstPostApprovalRepository;
use Flarum\Discussion\Discussion;
use Flarum\Extension\ExtensionManager;
use Flarum\Post\Event\Saving;
use Flarum\Settings\SettingsRepositoryInterface;

class UnapproveNewPosts
{
    protected $settings;
    protected $firstPosts;
    protected $extensions;

    public function __construct(SettingsRepositoryInterface $settings, FirstPostApprovalRepository $firstPosts, ExtensionManager $extensions)
    {
        $this->settings = $settings;
        $this->firstPosts = $firstPosts;
        $this->extensions = $extensions;
    }

    public function handle(Saving $event)
    {
        $post = $event->post;

        if ($post->exists || $event->actor->can('firstPostWithoutApproval', $post->discussion) || $this->isPrivate($post->discussion)) {
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

    protected function isPrivate(Discussion $discussion): bool
    {
        if ($this->extensions->isEnabled('fof-byobu')) {
            /** @var \FoF\Byobu\Discussion\Screener $byobu */
            $byobu = resolve(\FoF\Byobu\Discussion\Screener::class);
            return $byobu->fromDiscussion($discussion)->isPrivate();
        }

        return false;
    }
}
