import CourseForm from '@/components/forms/course-form';
import AppLayout from '@/layouts/app-layout';
import { Head, useForm } from '@inertiajs/react';
import React from 'react';

interface CourseCreatePageProps {
    teachers?: App.Data.UserData[];
}

export default function CourseCreatePage({ teachers = [] }: CourseCreatePageProps) {
    const { data, setData, post, processing, errors } = useForm({
        title: '',
        description: '',
        status: 'draft',
        is_published: false,
        teacher_id: '',
        start_date: '',
        end_date: '',
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
            title: 'Create Course',
            href: route('courses.create'),
        },
    ];

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('courses.store'));
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Create Course" />

            <div className="p-4">
                <div className="mb-6">
                    <h1 className="text-2xl font-bold">Create Course</h1>
                </div>

                <CourseForm
                    data={data}
                    setData={setData}
                    errors={errors}
                    processing={processing}
                    teachers={teachers}
                    onSubmit={handleSubmit}
                    submitButtonText="Create Course"
                />
            </div>
        </AppLayout>
    );
}
