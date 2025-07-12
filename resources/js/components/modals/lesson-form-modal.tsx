import LessonForm, { LessonFormData, LessonFormErrors } from '@/components/forms/lesson-form';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import React from 'react';

interface LessonFormModalProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    data: LessonFormData;
    setData: (key: string, value: string | boolean | number) => void;
    errors: LessonFormErrors;
    processing: boolean;
    onSubmit: (e: React.FormEvent) => void;
    isEditing?: boolean;
}

export default function LessonFormModal({
    open,
    onOpenChange,
    data,
    setData,
    errors,
    processing,
    onSubmit,
    isEditing = false,
}: LessonFormModalProps) {
    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>{isEditing ? 'Edit Lesson' : 'Create New Lesson'}</DialogTitle>
                    <DialogDescription>
                        {isEditing
                            ? 'Edit your lesson details. Lessons are the building blocks of your course.'
                            : 'Add a new lesson to your module. Lessons are the building blocks of your course.'}
                    </DialogDescription>
                </DialogHeader>
                <LessonForm data={data} setData={setData} errors={errors} processing={processing} onSubmit={onSubmit} isEditing={isEditing} />
            </DialogContent>
        </Dialog>
    );
}
