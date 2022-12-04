<?php

namespace Intimation\Catalyst;

class BlocksElement extends Element
{
    const REGISTER = 'register';
    const UNREGISTER = 'unregister';

    public function init()
    {
        if (array_key_exists(self::REGISTER, $this->config)) {
            $this->add($this->config[self::REGISTER]);
        }

        if (array_key_exists(self::UNREGISTER, $this->config)) {
            $this->remove($this->config[self::UNREGISTER]);
        }
    }

    protected function add(array $blocks)
    {
        array_map( 'register_block_type', $blocks );
    }

    protected function remove(array $blocks)
    {
        array_map( 'unregister_block_type', $blocks );
    }
}
