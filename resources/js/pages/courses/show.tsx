import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import AppLayout from '@/layouts/app-layout';
import { Course, Module, SharedData } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { format } from 'date-fns';

interface CourseShowPageProps {
    course: Course & {
        modules: Module[];
    };
}

export default function CourseShowPage({ course }: CourseShowPageProps) {
    const { auth } = usePage<SharedData>().props;
    const isAdmin = auth.roles?.includes('admin');

    const handleDelete = () => {
        router.delete(route('courses.destroy', { course: course.id }));
    };

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
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={course.title} />

            <div className="p-4">
                <div className="mb-6 flex items-center justify-between">
                    <h1 className="text-2xl font-bold">{course.title}</h1>
                    <div className="flex gap-2">
                        <Link href={route('courses.edit', { course: course.id })}>
                            <Button variant="outline">Edit Course</Button>
                        </Link>
                        <Link href={route('courses.students', { course: course.id })}>
                            <Button variant="outline">Manage Students</Button>
                        </Link>
                        {isAdmin && (
                            <Dialog>
                                <DialogTrigger asChild>
                                    <Button variant="destructive">Delete Course</Button>
                                </DialogTrigger>
                                <DialogContent>
                                    <DialogHeader>
                                        <DialogTitle>Delete Course</DialogTitle>
                                        <DialogDescription>
                                            Are you sure you want to delete this course? This action cannot be undone.
                                        </DialogDescription>
                                    </DialogHeader>
                                    <DialogFooter>
                                        <Button variant="outline" onClick={() => {}}>
                                            Cancel
                                        </Button>
                                        <Button variant="destructive" onClick={handleDelete}>
                                            Delete
                                        </Button>
                                    </DialogFooter>
                                </DialogContent>
                            </Dialog>
                        )}
                    </div>
                </div>

                <div className="grid grid-cols-1 gap-6 md:grid-cols-3">
                    <div className="md:col-span-2">
                        <Card>
                            {course.thumbnail && (
                                <div className="aspect-video overflow-hidden">
                                    <img src={course.thumbnail.url} alt={course.title} className="h-full w-full object-cover" />
                                </div>
                            )}
                            <CardHeader>
                                <CardTitle>Description</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <p className="whitespace-pre-line">{course.description}</p>
                            </CardContent>
                        </Card>

                        <div className="mt-6">
                            <h2 className="mb-4 text-xl font-bold">Course Content</h2>
                            {course.modules.length > 0 ? (
                                <div className="space-y-4">
                                    {course.modules.map((module) => (
                                        <Card key={module.id}>
                                            <CardHeader>
                                                <CardTitle>{module.title}</CardTitle>
                                                <CardDescription>{module.lessons.length} lessons</CardDescription>
                                            </CardHeader>
                                            <CardContent>
                                                <ul className="space-y-2">
                                                    {module.lessons.map((lesson) => (
                                                        <li key={lesson.id} className="flex items-center gap-2">
                                                            <span className="bg-primary/10 flex h-6 w-6 items-center justify-center rounded-full text-xs">
                                                                {lesson.order}
                                                            </span>
                                                            <span>{lesson.title}</span>
                                                        </li>
                                                    ))}
                                                </ul>
                                            </CardContent>
                                        </Card>
                                    ))}
                                </div>
                            ) : (
                                <Card>
                                    <CardContent className="py-6">
                                        <p className="text-center text-gray-500">No modules available for this course.</p>
                                    </CardContent>
                                </Card>
                            )}
                        </div>
                    </div>

                    <div>
                        <Card>
                            <CardHeader>
                                <CardTitle>Course Details</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="space-y-4">
                                    <div>
                                        <h3 className="text-sm font-medium text-gray-500">Status</h3>
                                        <p className="mt-1">
                                            <span
                                                className={`rounded-full px-2 py-1 text-xs ${
                                                    course.status === 'active'
                                                        ? 'bg-green-100 text-green-800'
                                                        : course.status === 'draft'
                                                          ? 'bg-yellow-100 text-yellow-800'
                                                          : 'bg-gray-100 text-gray-800'
                                                }`}
                                            >
                                                {course.status.toUpperCase()}
                                            </span>
                                        </p>
                                    </div>

                                    <div>
                                        <h3 className="text-sm font-medium text-gray-500">Instructor</h3>
                                        <p className="mt-1">{course.teacher ? course.teacher.name : 'No instructor assigned'}</p>
                                    </div>

                                    {course.start_date && (
                                        <div>
                                            <h3 className="text-sm font-medium text-gray-500">Start Date</h3>
                                            <p className="mt-1">{format(new Date(course.start_date), 'MMMM d, yyyy')}</p>
                                        </div>
                                    )}

                                    {course.end_date && (
                                        <div>
                                            <h3 className="text-sm font-medium text-gray-500">End Date</h3>
                                            <p className="mt-1">{format(new Date(course.end_date), 'MMMM d, yyyy')}</p>
                                        </div>
                                    )}

                                    <div>
                                        <h3 className="text-sm font-medium text-gray-500">Published</h3>
                                        <p className="mt-1">{course.is_published ? 'Yes' : 'No'}</p>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        {/* Student Information */}
                        <Card className="mt-4">
                            <CardHeader>
                                <CardTitle>Students</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="flex flex-col space-y-2">
                                    <p>
                                        {course.students && course.students.length > 0
                                            ? `${course.students.length} student${course.students.length > 1 ? 's' : ''} enrolled`
                                            : 'No students enrolled yet'}
                                    </p>
                                    <Link href={route('courses.students', { course: course.id })}>
                                        <Button variant="outline" size="sm">
                                            Manage Students
                                        </Button>
                                    </Link>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
