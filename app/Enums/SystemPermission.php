<?php

declare(strict_types=1);

namespace App\Enums;

enum SystemPermission: string
{
    case ManageUsers = 'users.manage';
    case CreateCourses = 'courses.create';
    case UpdateCourses = 'courses.update';
    case DeleteCourses = 'courses.delete';
    case AssignCourseTeachers = 'courses.assign-teachers';
    case AssignCourseAssistants = 'courses.assign-assistants';
    case ManageEnrollments = 'enrollments.manage';
    case PublishContent = 'content.publish';
    case RecordAttendance = 'attendance.record';
    case ManageExams = 'exams.manage';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(
            static fn (self $permission): string => $permission->value,
            self::cases(),
        );
    }
}
