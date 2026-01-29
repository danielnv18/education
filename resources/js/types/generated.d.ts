declare namespace App.Data {
    export type CourseData = {
        id: number;
        slug: string;
        title: string;
        description: string | null;
        status: App.Enums.CourseStatus;
        publishedAt: string | null;
        startsAt: string | null;
        endsAt: string | null;
        metadata: Array<never>;
        teacherId: number;
        modules: Array<App.Data.ModuleData> | null;
        isPublished: boolean;
    };
    export type LessonData = {
        id: number;
        moduleId: number;
        title: string;
        slug: string;
        summary: string | null;
        content: string | null;
        contentType: App.Enums.LessonContentType;
        order: number;
        durationMinutes: number | null;
        publishedAt: string | null;
        metadata: Array<never>;
        isPublished: boolean;
    };
    export type MediaData = {
        id: number;
        uuid: string;
        name: string;
        mimeType: string;
        size: number;
        url: string;
        previewUrl: string | null;
        collection: string;
    };
    export type ModuleData = {
        id: number;
        courseId: number;
        title: string;
        slug: string;
        description: string | null;
        order: number;
        publishedAt: string | null;
        isPublished: boolean;
        metadata: Array<never>;
        lessons: Array<App.Data.LessonData> | null;
    };
    export type RoleData = {
        name: string;
    };
    export type UserData = {
        id: number;
        name: string;
        email: string;
        emailVerifiedAt: string | null;
        createdAt: string;
        updatedAt: string;
        roles: Array<App.Data.RoleData>;
        avatar: string | null;
    };
}
declare namespace App.Enums {
    export type AssignmentType = 'essay' | 'upload' | 'quiz' | 'project';
    export type AttemptStatus =
        | 'in_progress'
        | 'submitted'
        | 'graded'
        | 'expired';
    export type AttendanceStatus = 'present' | 'late' | 'absent';
    export type CourseRole = 'teacher' | 'assistant' | 'student';
    export type CourseStatus = 'draft' | 'published' | 'archived';
    export type EnrollmentStatus = 'pending' | 'active' | 'inactive';
    export type InvitationStatus =
        | 'pending'
        | 'accepted'
        | 'declined'
        | 'revoked';
    export type LessonContentType =
        | 'markdown'
        | 'video_embed'
        | 'document_bundle';
    export type PermissionEnum =
        | 'users.provision'
        | 'invitations.resend'
        | 'invitations.revoke'
        | 'users.update'
        | 'users.password-reset'
        | 'users.delete'
        | 'users.restore'
        | 'roles.manage-global'
        | 'courses.assign-staff'
        | 'courses.manage-enrollments'
        | 'users.view-directory'
        | 'courses.manage'
        | 'courses.publish'
        | 'courses.manage-metadata'
        | 'courses.access-inactive'
        | 'modules.manage'
        | 'lessons.manage'
        | 'assignments.manage'
        | 'assignments.grade'
        | 'exams.manage'
        | 'exams.grade'
        | 'attendance.sessions.create'
        | 'attendance.records.manage'
        | 'attendance.analytics.view'
        | 'assessments.analytics.view'
        | 'settings.manage'
        | 'notifications.manage'
        | 'queues.manage'
        | 'dashboards.view';
    export type QuestionType =
        | 'single_choice'
        | 'multiple_choice'
        | 'rich_text';
    export type RoleEnum = 'admin' | 'content_manager' | 'teacher';
    export type SubmissionStatus =
        | 'draft'
        | 'submitted'
        | 'graded'
        | 'returned';
}
