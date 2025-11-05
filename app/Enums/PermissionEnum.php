<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Permission slugs registered with spatie/laravel-permission.
 */
enum PermissionEnum: string
{
    case ProvisionUsers = 'users.provision';
    case ResendInvitations = 'invitations.resend';
    case RevokeInvitations = 'invitations.revoke';
    case UpdateUsers = 'users.update';
    case SendPasswordResets = 'users.password-reset';
    case DeleteUsers = 'users.delete';
    case RestoreUsers = 'users.restore';
    case ManageGlobalRoles = 'roles.manage-global';
    case AssignCourseStaff = 'courses.assign-staff';
    case ManageEnrollments = 'courses.manage-enrollments';
    case ViewUserDirectory = 'users.view-directory';
    case ManageCourses = 'courses.manage';
    case PublishCourses = 'courses.publish';
    case ManageCourseMetadata = 'courses.manage-metadata';
    case AccessInactiveCourses = 'courses.access-inactive';
    case ManageModules = 'modules.manage';
    case ManageLessons = 'lessons.manage';
    case ManageAssignments = 'assignments.manage';
    case GradeAssignments = 'assignments.grade';
    case ManageExams = 'exams.manage';
    case GradeExams = 'exams.grade';
    case CreateAttendanceSessions = 'attendance.sessions.create';
    case RecordAttendance = 'attendance.records.manage';
    case ViewAttendanceAnalytics = 'attendance.analytics.view';
    case ViewAssessmentAnalytics = 'assessments.analytics.view';
    case ManagePlatformSettings = 'settings.manage';
    case ManageNotificationChannels = 'notifications.manage';
    case ManageQueues = 'queues.manage';
    case ViewDashboards = 'dashboards.view';
}
