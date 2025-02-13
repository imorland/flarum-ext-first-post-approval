<?php

namespace ClarkWinkelmann\FirstPostApproval\Tests\integration;

trait ExtensionDepsTrait
{
    public function extensionDeps(): void
    {
        $this->extension('clarkwinkelmann-first-post-approval');
        $this->extension('flarum-flags');
        $this->extension('flarum-approval');
        $this->extension('fof-byobu');
    }
}
