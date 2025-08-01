declare namespace App.Data {
    export type CourseData = {
        id: number | null;
        title: string;
        description: string | null;
        status: App.Enums.CourseStatus;
        cover: string | null;
        endDate: string | null;
        startDate: string | null;
        teacher: App.Data.UserData | null;
        isPublished: boolean;
        modules: Array<App.Data.ModuleData>;
        students: Array<App.Data.UserData>;
    };
    export type LessonData = {
        id: number | null;
        title: string;
        content: string;
        type: App.Enums.LessonType;
        order: number;
        isPublished: boolean;
    };
    export type ModuleData = {
        id: number | null;
        title: string;
        description: string | null;
        order: number;
        isPublished: boolean;
        lessons: Array<App.Data.LessonData>;
    };
    export type PermissionData = {
        name: string;
    };
    export type RoleData = {
        name: string;
    };
    export type UserData = {
        id: number | null;
        name: string;
        email: string;
        avatar: string | null;
        createdAt: string;
        emailVerifiedAt: string | null;
        roles: Array<App.Data.RoleData>;
    };
}
declare namespace App.Enums {
    export type CourseStatus = 'draft' | 'active' | 'archived';
    export type EnrollmentStatus = 'invited' | 'active' | 'dropped';
    export type LessonType = 'text' | 'video' | 'document' | 'link' | 'embed';
    export type PermissionEnum =
        | 'view any courses'
        | 'view course'
        | 'create course'
        | 'update course'
        | 'delete course'
        | 'restore course'
        | 'force delete course'
        | 'manage course content'
        | 'view course content'
        | 'view any users'
        | 'view user'
        | 'create user'
        | 'update user'
        | 'delete user'
        | 'manage users';
    export type UserRole = 'admin' | 'teacher' | 'student';
}
