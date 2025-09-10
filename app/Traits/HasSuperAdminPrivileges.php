<?php

namespace App\Traits;

use App\Enums\UserType;

trait HasSuperAdminPrivileges
{
    /**
     * Check if the user is a super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole(UserType::ADMIN->value);
    }

    /**
     * Check if the user has administrative privileges
     */
    public function isAdmin(): bool
    {
        return $this->hasAnyRole([
            UserType::ADMIN->value,
            UserType::HACKATHON_ADMIN->value
        ]);
    }

    /**
     * Grant super admin role to the user
     */
    public function makeSuperAdmin(): void
    {
        $this->syncRoles([UserType::ADMIN->value]);
    }

    /**
     * Check if user can perform any action (super admin bypass)
     */
    public function canDoAnything(): bool
    {
        return $this->isSuperAdmin();
    }

    /**
     * Override permission check for super admin
     */
    public function hasPermissionTo($permission, $guardName = null): bool
    {
        // Super admin can do everything
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Otherwise, check normal permissions using the aliased method
        return $this->hasBasePermissionTo($permission, $guardName);
    }

    /**
     * Override role check for super admin
     */
    public function hasRole($roles, string $guard = null): bool
    {
        if (is_string($roles) && $roles === UserType::ADMIN->value) {
            return $this->hasBaseRole($roles, $guard);
        }

        // Super admin has all roles virtually
        if ($this->isSuperAdmin()) {
            return true;
        }

        return $this->hasBaseRole($roles, $guard);
    }

    /**
     * Check if user can access admin panel
     */
    public function canAccessAdminPanel(): bool
    {
        return $this->hasPermissionTo('access_admin_panel') || $this->isSuperAdmin();
    }

    /**
     * Check if user can manage system settings
     */
    public function canManageSystem(): bool
    {
        return $this->hasPermissionTo('manage_system') || $this->isSuperAdmin();
    }

    /**
     * Check if user can manage other users
     */
    public function canManageUsers(): bool
    {
        return $this->hasPermissionTo('manage_users') || $this->isSuperAdmin();
    }

    /**
     * Check if user can view all data
     */
    public function canViewEverything(): bool
    {
        return $this->isSuperAdmin();
    }

    /**
     * Check if user can export all data
     */
    public function canExportAllData(): bool
    {
        return $this->hasPermissionTo('export_all_data') || $this->isSuperAdmin();
    }

    /**
     * Check if user can impersonate other users
     */
    public function canImpersonate(): bool
    {
        return $this->hasPermissionTo('impersonate_users') || $this->isSuperAdmin();
    }

    /**
     * Get all permissions (for super admin)
     */
    public function getAllPermissionsAttribute()
    {
        if ($this->isSuperAdmin()) {
            return \Spatie\Permission\Models\Permission::all();
        }

        return $this->permissions;
    }
}
