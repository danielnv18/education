import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import CourseLayout from '@/layouts/course/course-layout';
import { Course, Module } from '@/types';
import { Head } from '@inertiajs/react';

interface CourseContentPageProps {
    course: Course;
    modules: Module[];
}

export default function CourseContentPage({ course, modules }: CourseContentPageProps) {
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
            title: 'Content',
            href: '',
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Edit Content for Course ${course.title}`} />

            <CourseLayout course={course}>
                {modules.length > 0 ? (
                    <div className="space-y-4">
                        {modules.map((module) => (
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
            </CourseLayout>
        </AppLayout>
    );
}
