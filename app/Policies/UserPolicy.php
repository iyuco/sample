<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\User;

class UserPolicy
{
    use HandlesAuthorization;

    //自己只能编辑自己的资料
    public function update(User $currentUser, User $user)
    {
        return $currentUser->id === $user->id;
    }

    //只有管理员可以删除账号，且删除对象不是自己
    public function destroy(User $currentUser, User $user)
    {
        return $currentUser->is_admin && $currentUser->id !== $user->id;
    }
}
