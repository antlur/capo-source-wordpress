<?php

namespace CapoSourceWordpress\Types;

class Block
{
    public string $blockName;

    public array $attrs;

    public array $innerBlocks;

    public string $innerHTML;

    public array $innerContent;

    public function __construct($block)
    {
        $this->blockName    = $block->blockName;
        $this->attrs        = $block->attrs;
        $this->innerBlocks  = $block->innerBlocks;
        $this->innerHTML    = $block->innerHTML;
        $this->innerContent = $block->innerContent;
    }
}
