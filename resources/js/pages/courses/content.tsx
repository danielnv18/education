import DeleteConfirmationDialog from '@/components/modals/delete-confirmation-dialog';
import LessonFormModal from '@/components/modals/lesson-form-modal';
import ModuleFormModal from '@/components/modals/module-form-modal';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import CourseLayout from '@/layouts/course/course-layout';
import { Head, useForm } from '@inertiajs/react';
import { PlusCircle } from 'lucide-react';
import React, { useState } from 'react';

interface CourseContentPageProps {
    course: App.Data.CourseData;
    modules: App.Data.ModuleData[];
}

export default function CourseContentPage({ course, modules }: CourseContentPageProps) {
    const [moduleDialogOpen, setModuleDialogOpen] = useState(false);
    const [lessonDialogOpen, setLessonDialogOpen] = useState(false);
    const [selectedModuleId, setSelectedModuleId] = useState<number | null>(null);
    const [editingModule, setEditingModule] = useState<App.Data.ModuleData | null>(null);
    const [editingLesson, setEditingLesson] = useState<App.Data.LessonData | null>(null);

    const moduleForm = useForm({
        title: '',
        description: '',
        course_id: course.id,
        order: modules.length + 1,
        is_published: false as boolean,
    });

    const lessonForm = useForm({
        title: '',
        content: '',
        module_id: selectedModuleId,
        order: 0,
        type: 'text',
        is_published: false as boolean,
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
        if (editingModule) {
            moduleForm.put(route('courses.modules.update', { course: course.id, module: editingModule.id }), {
                onSuccess: () => {
                    setModuleDialogOpen(false);
                    setEditingModule(null);
                    moduleForm.reset();
                },
            });
        } else {
            moduleForm.post(route('courses.modules.store', { course: course.id }), {
                onSuccess: () => {
                    setModuleDialogOpen(false);
                    moduleForm.reset();
                },
            });
        }
    };

    const openEditModuleDialog = (module: App.Data.ModuleData) => {
        setEditingModule(module);
        moduleForm.setData({
            title: module.title,
            description: module.description || '',
            course_id: course.id,
            order: module.order,
            is_published: module.isPublished,
        });
        setModuleDialogOpen(true);
    };

    const handleDeleteModule = (module: App.Data.ModuleData) => {
        moduleForm.delete(route('courses.modules.destroy', { module: module.id, course: course.id }), {
            onFinish: () => {
                setEditingModule(null);
                setSelectedModuleId(null);
            },
        });
    };

    const handleLessonSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        if (editingLesson) {
            lessonForm.put(route('courses.lessons.update', { course: course.id, lesson: editingLesson.id }), {
                onSuccess: () => {
                    setLessonDialogOpen(false);
                    setEditingLesson(null);
                    lessonForm.reset();
                },
            });
        } else {
            lessonForm.post(route('courses.lessons.store', { course: course.id }), {
                onSuccess: () => {
                    setLessonDialogOpen(false);
                    lessonForm.reset();
                },
            });
        }
    };

    const openEditLessonDialog = (module: App.Data.ModuleData, lesson: App.Data.LessonData) => {
        setEditingLesson(lesson);
        setSelectedModuleId(module.id);
        lessonForm.setData({
            title: lesson.title,
            content: lesson.content || '',
            module_id: module.id,
            order: lesson.order,
            type: lesson.type,
            is_published: lesson.isPublished,
        });
        setLessonDialogOpen(true);
    };

    const handleDeleteLesson = (lesson: App.Data.LessonData) => {
        lessonForm.delete(route('courses.lessons.destroy', { lesson: lesson.id, course: course.id }), {
            onFinish: () => {
                setEditingModule(null);
                setSelectedModuleId(null);
            },
        });
    };

    const openLessonDialog = (moduleId: number | null, lessonCount: number) => {
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
                        isEditing={!!editingModule}
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
                    isEditing={!!editingLesson}
                />

                {modules.length > 0 ? (
                    <div className="space-y-4">
                        {modules.map((module) => (
                            <Card key={module.id}>
                                <CardHeader className="flex flex-row items-start justify-between space-y-0">
                                    <div>
                                        <CardTitle>{module.title}</CardTitle>
                                        <CardDescription>{module.lessons.length} lessons</CardDescription>
                                    </div>
                                    <div className="flex space-x-2">
                                        <Button variant="outline" size="sm" onClick={() => openEditModuleDialog(module)}>
                                            Edit
                                        </Button>
                                        <DeleteConfirmationDialog
                                            title="Delete Module"
                                            description="Are you sure you want to delete this module? This action cannot be undone and will also delete all lessons in this module."
                                            onDelete={() => handleDeleteModule(module)}
                                        />
                                    </div>
                                </CardHeader>
                                <CardContent>
                                    <ul className="space-y-2">
                                        {module.lessons.map((lesson) => (
                                            <li key={lesson.id} className="flex items-center justify-between gap-2">
                                                <div className="flex items-center gap-2">
                                                    <span className="bg-primary/10 flex h-6 w-6 items-center justify-center rounded-full text-xs">
                                                        {lesson.order}
                                                    </span>
                                                    <span>{lesson.title}</span>
                                                </div>
                                                <div className="flex space-x-2">
                                                    <Button variant="ghost" size="sm" onClick={() => openEditLessonDialog(module, lesson)}>
                                                        Edit
                                                    </Button>
                                                    <DeleteConfirmationDialog
                                                        title="Delete Lesson"
                                                        description="Are you sure you want to delete this lesson? This action cannot be undone."
                                                        onDelete={() => handleDeleteLesson(lesson)}
                                                    />
                                                </div>
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
