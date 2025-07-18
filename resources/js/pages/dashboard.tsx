import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { format } from 'date-fns';

interface DashboardIndexPageProps {
    teachingCourses: App.Data.CourseData[];
    enrolledCourses: App.Data.CourseData[];
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: route('dashboard'),
    },
];

export default function DashboardIndexPage({ teachingCourses, enrolledCourses }: DashboardIndexPageProps) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                {teachingCourses.length > 0 && (
                    <div className="space-y-4">
                        <h2 className="text-xl font-bold">Courses You Teach</h2>
                        <div className="grid auto-rows-min gap-4 md:grid-cols-3">
                            {teachingCourses.map((course) => (
                                <CourseCard key={course.id} course={course} />
                            ))}
                        </div>
                    </div>
                )}

                {enrolledCourses.length > 0 && (
                    <div className="space-y-4">
                        <h2 className="text-xl font-bold">Enrolled Courses</h2>
                        <div className="grid auto-rows-min gap-4 md:grid-cols-3">
                            {enrolledCourses.map((course) => (
                                <CourseCard key={course.id} course={course} />
                            ))}
                        </div>
                    </div>
                )}

                {teachingCourses.length === 0 && enrolledCourses.length === 0 && (
                    <div className="border-sidebar-border/70 dark:border-sidebar-border relative flex min-h-[50vh] flex-1 items-center justify-center overflow-hidden rounded-xl border md:min-h-min">
                        <div className="text-center">
                            <h2 className="mb-2 text-xl font-bold">No Courses Found</h2>
                            <p className="mb-4 text-gray-500">You are not teaching or enrolled in any courses yet.</p>
                            <Link href={route('courses.create')}>
                                <Button>Create a Course</Button>
                            </Link>
                        </div>
                    </div>
                )}
            </div>
        </AppLayout>
    );
}

interface CourseCardProps {
    course: App.Data.CourseData;
}

function CourseCard({ course }: CourseCardProps) {
    return (
        <Card className="overflow-hidden">
            {course.thumbnail ? (
                <div className="aspect-video overflow-hidden">
                    <img src={course.thumbnail} alt={course.title} className="h-full w-full object-cover" />
                </div>
            ) : (
                <div className="relative aspect-video overflow-hidden">
                    <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
                </div>
            )}
            <CardHeader>
                <CardTitle>{course.title}</CardTitle>
                <CardDescription>{course.teacher ? `Instructor: ${course.teacher.name}` : 'No instructor assigned'}</CardDescription>
            </CardHeader>
            <CardContent>
                <p className="line-clamp-2">{course.description}</p>
                {course.startDate && <p className="mt-2 text-xs text-gray-500">Starts: {format(new Date(course.startDate), 'MMM d, yyyy')}</p>}
            </CardContent>
            <CardFooter>
                <Link href={route('courses.show', { course: course.id })} className="w-full">
                    <Button variant="outline" className="w-full">
                        View Course
                    </Button>
                </Link>
            </CardFooter>
        </Card>
    );
}
