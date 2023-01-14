<?php

namespace App\Libraries;

use App\Models\Workspace;
use App\Models\WorkspaceUser;
use Illuminate\Support\Str;

trait HasWorkspaces
{
    /**
     * Determine if the given workspace is the current workspace.
     *
     * @param  mixed  $workspace
     * @return bool
     */
    public function isCurrentWorkspace($workspace)
    {
        return $workspace->id === $this->currentWorkspace->id;
    }

    /**
     * Get the current team of the user's context.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currentWorkspace()
    {
        if (is_null($this->current_workspace_id) && $this->id) {
            $this->switchWorkspace($this->defaultWorkspace());
        }

        return $this->belongsTo(Workspace::class, 'current_workspace_id');
    }

    /**
     * Switch the user's context to the given workspace.
     *
     * @param  mixed  $workspace
     * @return bool
     */
    public function switchWorkspace($workspace)
    {
        if (! $this->belongsToWorkspace($workspace)) {
            return false;
        }

        $this->forceFill([
            'current_workspace_id' => $workspace->id,
        ])->save();

        $this->setRelation('currentWorkspace', $workspace);

        return true;
    }

    /**
     * Get all of the workspaces the user owns or belongs to.
     *
     * @return \Illuminate\Support\Collection
     */
    public function allWorkspaces()
    {
        return $this->ownedWorkspaces->merge($this->workspaces);
    }

    /**
     * Get all of the workspaces the user owns.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ownedWorkspaces()
    {
        return $this->hasMany(Workspace::class);
    }

    /**
     * Get all of the workspaces the user belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function workspaces()
    {
        return $this->belongsToMany(Workspace::class, WorkspaceUser::class)
                        ->withPivot('role')
                        ->withTimestamps()
                        ->as('membership');
    }

    /**
     * Get the user's "default" workspace.
     *
     * @return \App\Models\Workspace
     */
    public function defaultWorkspace()
    {
        return $this->ownedTeams->where('default', true)->first();
    }

    /**
     * Determine if the user owns the given workspace.
     *
     * @param  mixed  $workspace
     * @return bool
     */
    public function ownsWorkspace($workspace)
    {
        if (is_null($workspace)) {
            return false;
        }

        return $this->id == $workspace->{$this->getForeignKey()};
    }

    /**
     * Determine if the user belongs to the given workspace.
     *
     * @param  mixed  $workspace
     * @return bool
     */
    public function belongsToWorkspace($workspace)
    {
        if (is_null($workspace)) {
            return false;
        }

        return $this->ownsWorkspace($workspace) || $this->teams->contains(function ($w) use ($workspace) {
            return $w->id === $workspace->id;
        });
    }

    /**
     * Get the role that the user has on the team.
     *
     * @param  mixed  $team
     * @return \Laravel\Jetstream\Role|null
     */
    public function workspaceRole($workspace)
    {
        if ($this->ownsWorkspace($workspace)) {
            return new OwnerRole;
        }

        if (! $this->belongsToWorkspace($workspace)) {
            return;
        }

        $role = $workspace->users
            ->where('id', $this->id)
            ->first()
            ->membership
            ->role;

        return $role ? Jetstream::findRole($role) : null;
    }

    /**
     * Determine if the user has the given role on the given workspace.
     *
     * @param  mixed  $workspace
     * @param  string  $role
     * @return bool
     */
    public function hasWorkspaceRole($workspace, string $role)
    {
        if ($this->ownsWorkspace($workspace)) {
            return true;
        }

        return $this->belongsToWorkspace($workspace) && optional(Jetstream::findRole($workspace->users->where(
            'id', $this->id
        )->first()->membership->role))->key === $role;
    }

    /**
     * Get the user's permissions for the given team.
     *
     * @param  mixed  $team
     * @return array
     */
    public function workspacePermissions($team)
    {
        if ($this->ownsWorkspace($team)) {
            return ['*'];
        }

        if (! $this->belongsToWorkspace($team)) {
            return [];
        }

        return (array) optional($this->teamRole($team))->permissions;
    }

    /**
     * Determine if the user has the given permission on the given team.
     *
     * @param  mixed  $team
     * @param  string  $permission
     * @return bool
     */
    public function hasWorkspacePermission($team, string $permission)
    {
        if ($this->ownsWorkspace($team)) {
            return true;
        }

        if (! $this->belongsToWorkspace($team)) {
            return false;
        }

        if (in_array(HasApiTokens::class, class_uses_recursive($this)) &&
            ! $this->tokenCan($permission) &&
            $this->currentAccessToken() !== null) {
            return false;
        }

        $permissions = $this->teamPermissions($team);

        return in_array($permission, $permissions) ||
               in_array('*', $permissions) ||
               (Str::endsWith($permission, ':create') && in_array('*:create', $permissions)) ||
               (Str::endsWith($permission, ':update') && in_array('*:update', $permissions));
    }
}
