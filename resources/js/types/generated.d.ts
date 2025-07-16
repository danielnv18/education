declare namespace App.Data {
    export type CourseData = {
        id: number;
        title: string;
        description: string | null;
        isPublished: boolean;
        status: App.Enums.CourseStatus;
        modules: Array<App.Data.ModuleData>;
        thumbnail: string;
        endDate: string | null;
        startDate: string | null;
        teacher: App.Data.UserData | null;
        students: Array<App.Data.UserData>;
    };
    export type LessonData = {
        id: number;
        title: string;
        content: string;
        type: App.Enums.LessonType;
        order: number;
    };
    export type ModuleData = {
        id: number;
        title: string;
        description: string | null;
        order: number;
        lessons: Array<App.Data.LessonData>;
    };
    export type RoleData = {
        name: string;
    };
    export type UserData = {
        id: number;
        name: string;
        email: string;
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
        | 'delete user';
    export type UserRole = 'admin' | 'teacher' | 'student';
}
