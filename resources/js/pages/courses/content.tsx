import LessonFormModal from '@/components/modals/lesson-form-modal';
import ModuleFormModal from '@/components/modals/module-form-modal';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import CourseLayout from '@/layouts/course/course-layout';
import { Course, Module } from '@/types';
import { Head, useForm } from '@inertiajs/react';
import { PlusCircle } from 'lucide-react';
import React, { useState } from 'react';

interface CourseContentPageProps {
    course: Course;
    modules: Module[];
}

export default function CourseContentPage({ course, modules }: CourseContentPageProps) {
    const [moduleDialogOpen, setModuleDialogOpen] = useState(false);
    const [lessonDialogOpen, setLessonDialogOpen] = useState(false);
    const [selectedModuleId, setSelectedModuleId] = useState<number | null>(null);

    const moduleForm = useForm({
        title: '',
        description: '',
        course_id: course.id,
        order: modules.length + 1,
        is_published: false,
    });

    const lessonForm = useForm({
        title: '',
        content: '',
        module_id: selectedModuleId,
        order: 0,
        type: 'text',
        is_published: false,
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
            title: 'Content',
            href: route('courses.content.index', { course: course.id }),
        },
    ];

    const handleModuleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        moduleForm.post(route('courses.modules.store', { course: course.id }), {
            onSuccess: () => {
                setModuleDialogOpen(false);
                moduleForm.reset();
            },
        });
    };

    const handleLessonSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        lessonForm.post(route('courses.lessons.store', { course: course.id }), {
            onSuccess: () => {
                setLessonDialogOpen(false);
                lessonForm.reset();
            },
        });
    };

    const openLessonDialog = (moduleId: number, lessonCount: number) => {
        setSelectedModuleId(moduleId);
        lessonForm.setData('module_id', moduleId);
        lessonForm.setData('order', lessonCount);
        setLessonDialogOpen(true);
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Edit Content for Course ${course.title}`} />

            <CourseLayout course={course}>
                <div className="mb-4 flex justify-end">
                    <ModuleFormModal
                        open={moduleDialogOpen}
                        onOpenChange={setModuleDialogOpen}
                        data={moduleForm.data}
                        setData={moduleForm.setData}
                        errors={moduleForm.errors}
                        processing={moduleForm.processing}
                        onSubmit={handleModuleSubmit}
                    />
                </div>

                <LessonFormModal
                    open={lessonDialogOpen}
                    onOpenChange={setLessonDialogOpen}
                    data={lessonForm.data}
                    setData={lessonForm.setData}
                    errors={lessonForm.errors}
                    processing={lessonForm.processing}
                    onSubmit={handleLessonSubmit}
                />

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
                                <CardFooter>
                                    <Button variant="outline" size="sm" onClick={() => openLessonDialog(module.id, module.lessons.length)}>
                                        <PlusCircle className="mr-2 h-4 w-4" />
                                        Add Lesson
                                    </Button>
                                </CardFooter>
                            </Card>
                        ))}
                    </div>
                ) : (
                    <Card>
                        <CardContent className="py-6">
                            <p className="text-center text-gray-500">No modules available for this course.</p>
                            <div className="mt-4 flex justify-center">
                                <Button variant="outline" onClick={() => setModuleDialogOpen(true)}>
                                    <PlusCircle className="mr-2 h-4 w-4" />
                                    Create Your First Module
                                </Button>
                            </div>
                        </CardContent>
                    </Card>
                )}
            </CourseLayout>
        </AppLayout>
    );
}
