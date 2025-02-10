<?php

namespace ClarkWinkelmann\FirstPostApproval;

use Flarum\Api\Serializer\ForumSerializer;
use Flarum\Approval\Event\PostWasApproved;
use Flarum\Discussion\Discussion;
use Flarum\Extend;
use Flarum\Post\Event\Saving;
use Flarum\User\User;

return [
    (new Extend\Frontend('admin'))
        ->js(__DIR__ . '/js/dist/admin.js'),

    (new Extend\Frontend('forum'))
        ->js(__DIR__ . '/js/dist/forum.js'),

    new Extend\Locales(__DIR__ . '/resources/locale'),

    (new Extend\Model(User::class))
        ->cast('first_post_approval_count', 'int')
        ->cast('first_discussion_approval_count', 'int'),

    (new Extend\Event())
        ->listen(PostWasApproved::class, Listeners\CountPostApprovals::class)
        ->listen(Saving::class, Listeners\UnapproveNewPosts::class),

    (new Extend\Settings())
        ->default('clarkwinkelmann-first-post-approval.discussionCount', 1)
        ->default('clarkwinkelmann-first-post-approval.postCount', 1),

    (new Extend\Conditional())
        ->whenExtensionEnabled('fof-byobu', fn () => [
            (new Extend\ApiSerializer(ForumSerializer::class))
                ->attributes(Api\ForumAttributes::class)
        ]),
];
