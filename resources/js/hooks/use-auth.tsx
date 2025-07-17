import { SharedData } from '@/types';
import { usePage } from '@inertiajs/react';
import { useMemo } from 'react';

/**
 * Custom React hook that checks if the current authenticated user has a specific role
 * @param role - The role to check for
 * @returns boolean indicating whether the user has the specified role
 * @throws Error if role parameter is empty or invalid
 */
export function useHasRole(role: App.Enums.UserRole): boolean {
    const { auth } = usePage<SharedData>().props;

    if (!role) {
        throw new Error('Role parameter is required');
    }

    return useMemo(() => {
        if (!auth.user) {
            return false;
        }
        return auth.roles.some((userRole) => userRole.name === role);
    }, [auth.user, auth.roles, role]);
}

/**
 * Custom hook to check if the current user has a specific permission
 */
export function useHasPermissionTo(permission: App.Enums.PermissionEnum): boolean {
    const { auth } = usePage<SharedData>().props;

    if (!permission) {
        throw new Error('Permission parameter is required');
    }

    return useMemo(() => {
        if (!auth.user) {
            return false;
        }
        return auth.permissions.some((perm) => perm.name === permission);
    }, [auth.user, auth.permissions, permission]);
}
