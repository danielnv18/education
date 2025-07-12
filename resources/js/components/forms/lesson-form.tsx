import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { DialogFooter } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import React from 'react';

export interface LessonFormData {
    title: string;
    content: string;
    module_id: number | null;
    order: number;
    type: string;
    is_published: boolean;
}

export interface LessonFormErrors {
    title?: string;
    content?: string;
    module_id?: string;
    order?: string;
    type?: string;
    is_published?: string;
}

interface LessonFormProps {
    data: LessonFormData;
    setData: (key: string, value: string | boolean | number) => void;
    errors: LessonFormErrors;
    processing: boolean;
    onSubmit: (e: React.FormEvent) => void;
    isEditing?: boolean;
}

export default function LessonForm({ data, setData, errors, processing, onSubmit, isEditing = false }: LessonFormProps) {
    return (
        <form onSubmit={onSubmit}>
            <div className="space-y-4 py-4">
                <div className="space-y-2">
                    <Label htmlFor="lesson-title">Title</Label>
                    <Input id="lesson-title" value={data.title} onChange={(e) => setData('title', e.target.value)} />
                    <InputError message={errors.title} />
                </div>
                <div className="space-y-2">
                    <Label htmlFor="lesson-content">Content</Label>
                    <Textarea id="lesson-content" value={data.content} onChange={(e) => setData('content', e.target.value)} />
                    <InputError message={errors.content} />
                </div>
                <div className="space-y-2">
                    <Label htmlFor="lesson-type">Type</Label>
                    <Select value={data.type} onValueChange={(value) => setData('type', value)}>
                        <SelectTrigger>
                            <SelectValue placeholder="Select lesson type" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="text">Text</SelectItem>
                            <SelectItem value="video">Video</SelectItem>
                            <SelectItem value="document">Document</SelectItem>
                            <SelectItem value="link">Link</SelectItem>
                            <SelectItem value="embed">Embed</SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError message={errors.type} />
                </div>
                <div className="space-y-2">
                    <Label htmlFor="lesson-order">Order</Label>
                    <Input id="lesson-order" type="number" value={data.order} onChange={(e) => setData('order', parseInt(e.target.value))} />
                    <InputError message={errors.order} />
                </div>
                <div className="flex items-center space-x-2">
                    <Checkbox
                        id="lesson-is_published"
                        checked={data.is_published}
                        onCheckedChange={(checked) => setData('is_published', !!checked)}
                    />
                    <Label htmlFor="lesson-is_published">Published</Label>
                    <InputError message={errors.is_published} />
                </div>
            </div>
            <DialogFooter>
                <Button type="submit" disabled={processing}>
                    {isEditing ? 'Update Lesson' : 'Create Lesson'}
                </Button>
            </DialogFooter>
        </form>
    );
}
