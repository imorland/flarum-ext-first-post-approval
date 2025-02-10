<?php

namespace ClarkWinkelmann\FirstPostApproval\Repository;

use Carbon\Carbon;
use Flarum\Flags\Flag;
use Flarum\Post\Post;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\User;

class FirstPostApprovalRepository
{
    protected $settings;
    
    public function __construct(SettingsRepositoryInterface $settings)
    {
        $this->settings = $settings;
    }

    public function flagPost(Post $post): void
    {
        $post->afterSave(function (Post $post) {
            if ($post->number === 1) {
                $post->discussion->is_approved = false;
                $post->discussion->save();
            }

            $flag = new Flag();

            $flag->post_id = $post->id;
            $flag->type = 'approval';
            $flag->created_at = Carbon::now();

            $flag->save();
        });
    }

    public function isUserSubjectToFPA(User $user): bool
    {
        // If user has bypass permission, then early return
        if ($user->can('firstPostWithoutApproval')) {
            return false;
        }
        
        if ($user->comment_count < $this->requiredPostCount() || $user->discussion_count < $this->requiredDiscussionCount()) {
            return true;
        }

        return false;
    }

    public function requiredDiscussionCount(): int
    {
        return $this->settings->get('clarkwinkelmann-first-post-approval.discussionCount');
    }

    public function requiredPostCount(): int
    {
        return $this->settings->get('clarkwinkelmann-first-post-approval.postCount');
    }
}
