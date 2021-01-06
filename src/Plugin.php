<?php

namespace CapoSourceWordpress;

use Capo\Services\Plugins\Plugin as BasePlugin;

class Plugin extends BasePlugin
{
    public function __construct(array $options = [])
    {
        $this->serviceProvider = CapoSourceWordpressServiceProvider::class;

        $this->options = $options;
    }
}
