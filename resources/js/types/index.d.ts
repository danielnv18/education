import { LucideIcon } from 'lucide-react';
import type { Config } from 'ziggy-js';

export interface Auth {
    user: App.Data.UserData;
    roles: App.Data.RoleData[];
    permissions: App.Data.PermissionData[];
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavGroup {
    title: string;
    items: NavItem[];
}

export interface NavItem {
    title: string;
    href: string;
    icon?: LucideIcon | null;
    isActive?: boolean;
}

export interface SharedData {
    name: string;
    auth: Auth;
    ziggy: Config & { location: string };
    sidebarOpen: boolean;
    [key: string]: unknown;
}

export interface PageProps {
    [key: string]: unknown;
}

export interface Role {
    name: string;
}

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    emailVerifiedAt: string | null;
    roles: Role[];
    created_at: string;
}

export interface File {
    id: number;
    name: string;
    url: string;
    mime_type: string;
    size: number;
}

export interface Course {
    id: number;
    title: string;
    description: string | null;
    status: 'draft' | 'active' | 'archived';
    is_published: boolean;
    teacher_id: number | null;
    thumbnail_id: number | null;
    start_date: string | null;
    end_date: string | null;
    created_at: string;
    updated_at: string;
    teacher?: User;
    thumbnail?: File;
    students?: User[];
}

export interface Lesson {
    id: number;
    title: string;
    content: string | null;
    module_id: number;
    order: number;
    is_published: boolean;
    type: 'video' | 'text' | 'document' | 'link' | 'embed';
}

export interface Module {
    id: number;
    title: string;
    description: string | null;
    course_id: number;
    order: number;
    lessons: Lesson[];
    is_published: boolean;
}
