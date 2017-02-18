<?php

namespace Belt\Content\Policies;

use Belt\Core\User;
use Belt\Core\Policies\BaseAdminPolicy;
use Belt\Content\Handle;

/**
 * Class HandlePolicy
 * @package Belt\Content\Policies
 */
class HandlePolicy extends BaseAdminPolicy
{

    /**
     * Determine whether the user can view the object.
     *
     * @param  User $auth
     * @param  Handle $object
     * @return mixed
     */
    public function view(User $auth, $object)
    {
        return true;
    }
}