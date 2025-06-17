import { SharedData } from '@/types';
import { usePage } from '@inertiajs/react';

/**
 * Custom hook to check if the current user has a specific role
 */
export function useHasRole() {
    const { auth } = usePage<SharedData>().props;

    return (role: string): boolean => {
        if (!auth.user) {
            return false;
        }

        return auth.roles.includes(role);
    };
}

/**
 * Custom hook to check if the current user has a specific permission
 */
export function useCan() {
    const { auth } = usePage<SharedData>().props;

    return (permission: string): boolean => {
        if (!auth.user) {
            return false;
        }

        return auth.permissions.includes(permission);
    };
}
