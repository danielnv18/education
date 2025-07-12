import ModuleForm, { ModuleFormData, ModuleFormErrors } from '@/components/forms/module-form';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { PlusCircle } from 'lucide-react';
import React from 'react';

interface ModuleFormModalProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    data: ModuleFormData;
    setData: (key: string, value: string | boolean | number) => void;
    errors: ModuleFormErrors;
    processing: boolean;
    onSubmit: (e: React.FormEvent) => void;
    triggerButton?: React.ReactNode;
    isEditing?: boolean;
}

export default function ModuleFormModal({
    open,
    onOpenChange,
    data,
    setData,
    errors,
    processing,
    onSubmit,
    triggerButton,
    isEditing = false,
}: ModuleFormModalProps) {
    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            {triggerButton ? (
                <DialogTrigger asChild>{triggerButton}</DialogTrigger>
            ) : (
                <DialogTrigger asChild>
                    <Button>
                        <PlusCircle className="mr-2 h-4 w-4" />
                        Add Module
                    </Button>
                </DialogTrigger>
            )}
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>{isEditing ? 'Edit Module' : 'Create New Module'}</DialogTitle>
                    <DialogDescription>
                        {isEditing
                            ? 'Edit your module details. Modules help organize your course content.'
                            : 'Add a new module to your course. Modules help organize your course content.'}
                    </DialogDescription>
                </DialogHeader>
                <ModuleForm data={data} setData={setData} errors={errors} processing={processing} onSubmit={onSubmit} isEditing={isEditing} />
            </DialogContent>
        </Dialog>
    );
}
