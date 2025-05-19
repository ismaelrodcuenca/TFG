<?php

namespace app\Helpers;


use DB;
use Filament\Notifications\Notification;

class PermissionHelper
{

    public static function developMode(): bool
    {
        return true;
    }
    public static function actualRol(): int
    {
        return session('rol_id', 0);
    }

    public static function hasRole(): bool
    {
        return session('rol_id') != 0;
    }
    /**
     * Check if the current user has administrative privileges.
     *
     * This method determines whether the user has the necessary permissions
     * to be considered an administrator.
     *
     * @return bool Returns true if the user is an administrator, false otherwise.
     */
    public static function isAdmin(): bool
    {
        return in_array(self::actualRol(), [ADMIN_ROL]);
    }
    /**
     * Checks if the current user has the role of a technician or upper.
     *
     * @return bool Returns true if the user is a technician, otherwise false.
     */
    public static function isTechnicion(): bool
    {
        return in_array(self::actualRol(), [TECHNICIAN_ROL, ADMIN_ROL, MANAGER_ROL]);
    }

    /**
     * Checks if the current user has manager or upperlevel of permissions.
     *
     * @return bool Returns true if the user is a manager, false otherwise.
     */
    public static function isManager(): bool
    {
        Notification::make('ROL ACTUAL: '. self::actualRol());
        return in_array(self::actualRol(), [ADMIN_ROL, MANAGER_ROL]);
    }

    /**
     * Checks if the current user has the role of a salesperson or upper.
     *
     * @return bool Returns true if the user is a salesperson, false otherwise.
     */
    public static function isSalesperson(): bool
    {
        return in_array(self::actualRol(), [SALESPERSON_ROL, MANAGER_ROL, ADMIN_ROL]);
    }

    /**
     * Determines if the provided value satisfies the "something else" condition.
     *
     * @param int|string $else The value to be checked. It can be an integer or a string.
     * @return bool Returns true if the condition is met, otherwise false.
     */
    public static function isSomethingElse(int|string $else): bool
    {
        if (is_int($else)) {
            $rol = DB::table("roles")->where("id", $else)->first();
        }
        if (is_string($else)) {
            $rol = DB::table("roles")->where("name", $else)->first();
        }
        if (!$rol) {
            return in_array(self::actualRol(), [$rol->id]);
        }
        return false;
    }


    /**
     * Check if the current user does not have administrative privileges.
     *
     * @return bool Returns true if the user is not an administrator, false otherwise.
     */
    public static function isNotAdmin(): bool
    {
        return !self::isAdmin();
    }

    /**
     * Checks if the current user does not have the role of a technician or upper.
     *
     * @return bool Returns true if the user is not a technician, otherwise false.
     */
    public static function isNotTechnicion(): bool
    {
        return !self::isTechnicion();
    }

    /**
     * Checks if the current user does not have manager or upper-level permissions.
     *
     * @return bool Returns true if the user is not a manager, false otherwise.
     */
    public static function isNotManager(): bool
    {
        return !self::isManager();
    }

    /**
     * Checks if the current user does not have the role of a salesperson or upper.
     *
     * @return bool Returns true if the user is not a salesperson, false otherwise.
     */
    public static function isNotSalesperson(): bool
    {
        return !self::isSalesperson();
    }

    /**
     * Determines if the provided value does not satisfy the "something else" condition.
     *
     * @param int|string $else The value to be checked. It can be an integer or a string.
     * @return bool Returns true if the condition is not met, otherwise false.
     */
    public static function isNotSomethingElse(int|string $else): bool
    {
        return !self::isSomethingElse($else);
    }

    /**
     * Checks if the current user has a valid role.
     *
     * @return bool Returns false if there is no role, true otherwise.
     */
}