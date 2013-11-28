<?php

namespace CodeLovers\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class CodeLoversUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
