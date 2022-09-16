<?php

namespace App\Policies;

use App\Models\Member;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

abstract class BasePolicy
{
    use HandlesAuthorization;

    protected string $modelClass;

    protected string $resourceSlug;

    /**
     * Get Model Class this controller caters.
     *
     * @return string
     */
    abstract protected function getModelClass() : string;

    abstract protected function getResourceSlug() : string;

    /**
     * BasePolicy constructor.
     */
    public function __construct()
    {
        $this->modelClass = $this->getModelClass();
        $this->resourceSlug = $this->getResourceSlug();
    }

    /**
     * Run before to determine if user is a Super Admin.
     *
     * @param User|Member $user
     * @return bool|null
     */
    public function before(User|Member $user): ?bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return true;
    }

    /**
     * Determine whether the user can view all models.
     *
     * @param User|Member $user
     * @return bool
     */
    public function index(User|Member $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User|Member $user
     * @param Model|null $model
     * @return bool
     */
    public function view(User|Member $user, ?Model $model = null): bool
    {
        return $this->hasPermission($user, 'show');
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param User|Member $user
     * @return bool
     */
    public function viewAny(User|Member $user): bool
    {
        return $this->hasPermission($user, 'show');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User|Member $user
     * @return bool
     */
    public function create(User|Member $user): bool
    {
        return $this->hasPermission($user, 'store');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User|Member $user
     * @param Model|null $model
     * @return bool
     */
    public function update(User|Member $user, ?Model $model = null): bool
    {
        return $this->hasPermission($user, 'update');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User|Member $user
     * @return bool
     */
    public function delete(User|Member $user): bool
    {
        return $this->hasPermission($user, 'destroy');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User|Member $user
     * @return bool
     */
    public function forceDelete(User|Member $user): bool
    {
        return false;
    }

    /**
     * @param User|Member $user
     * @return bool
     */
    public function detach(User|Member $user): bool
    {
        return true;
    }

    /**
     * @param User|Member $user
     * @return bool
     */
    public function attach(User|Member $user): bool
    {
        return true;
    }

    public function uploadFiles(User|Member $user): bool
    {
        return true;
    }

    protected function hasPermission($user, $slug): bool
    {
        $userPermissions = Cache::remember($user->id.'-'.$this->resourceSlug.'.'.$slug.'.user-permissions', now()->addMinutes(5), function () use ($slug, $user) {
            return $user->permissions()->where('route', $this->resourceSlug.'.'.$slug)->first();
        });

        $groupPermissions = Cache::remember($user->id.'-'.$this->resourceSlug.'.'.$slug.'.group-permissions', now()->addMinutes(5), function () use ($slug, $user) {
            return $user->groups()
                ->whereHas(
                    'permissions',
                    function ($query) use ($slug) {
                        $query->where('route', $this->resourceSlug.'.'.$slug);
                    }
                )
                ->first();
        });

        if ($userPermissions || $groupPermissions){
            return true;
        }

        return false;
    }
}
