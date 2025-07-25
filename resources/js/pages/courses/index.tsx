import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import { Head, Link } from '@inertiajs/react';
import { format } from 'date-fns';

interface CourseIndexPageProps {
    courses: App.Data.CourseData[];
}

const breadcrumbs = [
    {
        title: 'Dashboard',
        href: route('dashboard'),
    },
    {
        title: 'Courses',
        href: route('courses.index'),
    },
];

export default function CourseIndexPage({ courses }: CourseIndexPageProps) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Courses" />

            <div className="p-4">
                <div className="mb-6 flex items-center justify-between">
                    <h1 className="text-2xl font-bold">Courses</h1>
                    <Link href={route('courses.create')}>
                        <Button>Create Course</Button>
                    </Link>
                </div>

                <div className="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                    {courses.map((course) => (
                        <Card key={course.id} className="overflow-hidden">
                            {course.cover && (
                                <div className="aspect-video overflow-hidden">
                                    <img src={course.cover} alt={course.title} className="h-full w-full object-cover" />
                                </div>
                            )}
                            <CardHeader>
                                <CardTitle>{course.title}</CardTitle>
                                <CardDescription>{course.teacher ? `Instructor: ${course.teacher.name}` : 'No instructor assigned'}</CardDescription>
                            </CardHeader>
                            <CardContent>
                                <p className="line-clamp-3">{course.description}</p>
                                <div className="mt-2 flex items-center gap-2">
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
                                    {course.startDate && (
                                        <span className="text-xs text-gray-500">Starts: {format(new Date(course.startDate), 'MMM d, yyyy')}</span>
                                    )}
                                </div>
                            </CardContent>
                            <CardFooter>
                                <Link href={route('courses.show', { course: course.id })} className="w-full">
                                    <Button variant="outline" className="w-full">
                                        View Course
                                    </Button>
                                </Link>
                            </CardFooter>
                        </Card>
                    ))}
                </div>

                {courses.length === 0 && (
                    <div className="py-12 text-center">
                        <p className="text-gray-500">No courses found.</p>
                    </div>
                )}

                {/* Pagination can be added here */}
            </div>
        </AppLayout>
    );
}
