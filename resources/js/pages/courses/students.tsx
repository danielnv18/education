import StudentTable from '@/components/courses/student-table';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import AppLayout from '@/layouts/app-layout';
import CourseLayout from '@/layouts/course/course-layout';
import { Course, SharedData, User } from '@/types';
import { Head, router, usePage } from '@inertiajs/react';
import { useState } from 'react';

interface CourseStudentsPageProps {
    course: Course;
    availableStudents: User[];
}

export default function CourseStudentsPage({ course, availableStudents }: CourseStudentsPageProps) {
    const { auth } = usePage<SharedData>().props;
    const isAdmin = auth.roles?.includes('admin');
    const isTeacher = auth.roles?.includes('teacher');
    const canEnrollStudents = isAdmin || (isTeacher && course.teacher_id === auth.user?.id);
    const [selectedStudentIds, setSelectedStudentIds] = useState<Record<string, boolean>>({});
    const [dialogOpen, setDialogOpen] = useState(false);

    const breadcrumbs = [
        {
            title: 'Dashboard',
            href: route('dashboard'),
        },
        {
            title: 'Courses',
            href: route('courses.index'),
        },
        {
            title: course.title,
            href: route('courses.show', { course: course.id }),
        },
        {
            title: 'Students',
            href: route('courses.students.index', { course: course.id }),
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`${course.title} - Students`} />

            <CourseLayout course={course}>
                <div className="space-y-6">
                    {/* Enrolled Students Section */}
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between">
                            <CardTitle>Enrolled Students</CardTitle>
                            {canEnrollStudents && availableStudents.length > 0 && (
                                <Dialog open={dialogOpen} onOpenChange={setDialogOpen}>
                                    <DialogTrigger asChild>
                                        <Button size="sm">Enroll Students</Button>
                                    </DialogTrigger>
                                    <DialogContent className="max-w-lg">
                                        <DialogHeader>
                                            <DialogTitle>Enroll Students</DialogTitle>
                                        </DialogHeader>
                                        <form
                                            onSubmit={async (e) => {
                                                e.preventDefault();
                                                const selectedIds = Object.entries(selectedStudentIds)
                                                    .filter(([, isSelected]) => isSelected)
                                                    .map(([id]) => id);
                                                if (selectedIds.length === 0) {
                                                    return;
                                                }
                                                router.post(
                                                    route('courses.students.store', { course: course.id }),
                                                    {
                                                        student_ids: selectedIds,
                                                    },
                                                    {
                                                        onSuccess: () => setDialogOpen(false),
                                                    },
                                                );
                                            }}
                                        >
                                            <div className="space-y-4">
                                                <div>
                                                    <label className="mb-2 block text-sm font-medium text-gray-700">Select Students</label>
                                                    <div className="max-h-60 space-y-2 overflow-y-auto rounded-md border p-2">
                                                        {availableStudents.map((student) => (
                                                            <div key={student.id} className="flex items-center space-x-2">
                                                                <Checkbox
                                                                    id={`student-${student.id}`}
                                                                    checked={selectedStudentIds[student.id] || false}
                                                                    onCheckedChange={(checked) => {
                                                                        setSelectedStudentIds({
                                                                            ...selectedStudentIds,
                                                                            [student.id]: !!checked,
                                                                        });
                                                                    }}
                                                                />
                                                                <label htmlFor={`student-${student.id}`} className="cursor-pointer text-sm">
                                                                    {student.name} ({student.email})
                                                                </label>
                                                            </div>
                                                        ))}
                                                    </div>
                                                </div>
                                                <Button type="submit" disabled={Object.values(selectedStudentIds).filter(Boolean).length === 0}>
                                                    Enroll Selected Students
                                                </Button>
                                            </div>
                                        </form>
                                    </DialogContent>
                                </Dialog>
                            )}
                        </CardHeader>
                        <CardContent>
                            {course.students && course.students.length > 0 ? (
                                <StudentTable students={course.students} />
                            ) : (
                                <p className="text-gray-500">No students enrolled in this course yet.</p>
                            )}
                        </CardContent>
                    </Card>
                </div>
            </CourseLayout>
        </AppLayout>
    );
}
