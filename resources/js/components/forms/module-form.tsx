import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { DialogFooter } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import React from 'react';

export interface ModuleFormData {
    title: string;
    description: string;
    course_id: number;
    order: number;
    is_published: boolean;
}

export interface ModuleFormErrors {
    title?: string;
    description?: string;
    course_id?: string;
    order?: string;
    is_published?: string;
    [key: string]: string | undefined;
}

interface ModuleFormProps {
    data: ModuleFormData;
    setData: (key: string, value: string | boolean | number) => void;
    errors: ModuleFormErrors;
    processing: boolean;
    onSubmit: (e: React.FormEvent) => void;
    isEditing?: boolean;
}

export default function ModuleForm({ data, setData, errors, processing, onSubmit, isEditing = false }: ModuleFormProps) {
    return (
        <form onSubmit={onSubmit}>
            <div className="space-y-4 py-4">
                <div className="space-y-2">
                    <Label htmlFor="title">Title</Label>
                    <Input id="title" value={data.title} onChange={(e) => setData('title', e.target.value)} />
                    <InputError message={errors.title} />
                </div>
                <div className="space-y-2">
                    <Label htmlFor="description">Description</Label>
                    <Textarea id="description" value={data.description} onChange={(e) => setData('description', e.target.value)} />
                    <InputError message={errors.description} />
                </div>
                <div className="space-y-2">
                    <Label htmlFor="order">Order</Label>
                    <Input id="order" type="number" value={data.order} onChange={(e) => setData('order', parseInt(e.target.value))} />
                    <InputError message={errors.order} />
                </div>
                <div className="flex items-center space-x-2">
                    <Checkbox id="is_published" checked={data.is_published} onCheckedChange={(checked) => setData('is_published', !!checked)} />
                    <Label htmlFor="is_published">Published</Label>
                    <InputError message={errors.is_published} />
                </div>
            </div>
            <DialogFooter>
                <Button type="submit" disabled={processing}>
                    {isEditing ? 'Update Module' : 'Create Module'}
                </Button>
            </DialogFooter>
        </form>
    );
}
