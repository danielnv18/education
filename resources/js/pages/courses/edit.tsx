import CourseForm from '@/components/forms/course-form';
import AppLayout from '@/layouts/app-layout';
import CourseLayout from '@/layouts/course/layout';
import { Course, User } from '@/types';
import { Head, useForm } from '@inertiajs/react';

interface CourseEditPageProps {
    course: Course;
    teachers?: User[];
}

export default function CourseEditPage({ course, teachers = [] }: CourseEditPageProps) {
    const { data, setData, put, processing, errors } = useForm({
        title: course.title,
        description: course.description || '',
        status: course.status,
        is_published: course.is_published,
        teacher_id: course.teacher_id ? String(course.teacher_id) : '',
        start_date: course.start_date ? new Date(course.start_date).toISOString().split('T')[0] : '',
        end_date: course.end_date ? new Date(course.end_date).toISOString().split('T')[0] : '',
    });

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
            title: 'Edit',
            href: route('courses.edit', { course: course.id }),
        },
    ];

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        put(route('courses.update', { course: course.id }));
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Edit ${course.title}`} />

            <CourseLayout course={course}>
                <CourseForm
                    data={data}
                    setData={setData}
                    errors={errors}
                    processing={processing}
                    teachers={teachers}
                    onSubmit={handleSubmit}
                    submitButtonText="Update Course"
                />
            </CourseLayout>
        </AppLayout>
    );
}
