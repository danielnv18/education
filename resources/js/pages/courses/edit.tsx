import CourseForm from '@/components/forms/course-form';
import AppLayout from '@/layouts/app-layout';
import CourseLayout from '@/layouts/course/course-layout';
import { Head, useForm } from '@inertiajs/react';

interface CourseEditPageProps {
    course: App.Data.CourseData;
    teachers?: App.Data.UserData[];
}

export default function CourseEditPage({ course, teachers = [] }: CourseEditPageProps) {
    const { data, setData, put, processing, errors } = useForm({
        title: course.title,
        description: course.description || '',
        status: course.status,
        is_published: course.isPublished,
        teacher_id: course.teacher?.id ? String(course.teacher.id) : '',
        start_date: course.startDate ? new Date(course.startDate).toISOString().split('T')[0] : '',
        end_date: course.endDate ? new Date(course.endDate).toISOString().split('T')[0] : '',
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
