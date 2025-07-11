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
}

export default function LessonFormModal({ open, onOpenChange, data, setData, errors, processing, onSubmit }: LessonFormModalProps) {
    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Create New Lesson</DialogTitle>
                    <DialogDescription>Add a new lesson to your module. Lessons are the building blocks of your course.</DialogDescription>
                </DialogHeader>
                <LessonForm data={data} setData={setData} errors={errors} processing={processing} onSubmit={onSubmit} />
            </DialogContent>
        </Dialog>
    );
}
