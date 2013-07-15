<?php

namespace Twinpeaks\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class TPUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
