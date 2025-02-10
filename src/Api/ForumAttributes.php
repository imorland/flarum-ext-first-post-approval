<?php

namespace ClarkWinkelmann\FirstPostApproval\Api;

use ClarkWinkelmann\FirstPostApproval\Repository\FirstPostApprovalRepository;
use Flarum\Api\Serializer\ForumSerializer;
use Flarum\Extension\ExtensionManager;

class ForumAttributes
{
    protected $extensions;
    protected $firstPosts;
    
    public function __construct(ExtensionManager $extensions, FirstPostApprovalRepository $firstPosts)
    {
        $this->extensions = $extensions;
        $this->firstPosts = $firstPosts;
    }
    
    public function __invoke(ForumSerializer $serializer, $model, array $attributes): array
    {
        $actor = $serializer->getActor();

        // TODO: This is a temporary solution to disable the private discussion features, frontend only.
        // This needs to be refactored to be more robust and to work with the backend as well.
        if ($this->extensions->isEnabled('fof-byobu') && $this->firstPosts->isUserSubjectToFPA($actor)) {

            $attributes['canStartPrivateDiscussion'] = false;
            $attributes['canStartPrivateDiscussionWithUsers'] = false;
            $attributes['canAddMoreThanTwoUserRecipients'] = false;
            $attributes['canStartPrivateDiscussionWithGroups'] = false;
            $attributes['canStartPrivateDiscussionWithBlockers'] = false;
        }

        return $attributes;
    }
}
