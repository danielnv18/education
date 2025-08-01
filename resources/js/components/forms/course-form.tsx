import CoverImageUpload from '@/components/cover-image-upload';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import React from 'react';

export interface CourseFormData {
    title: string;
    description: string;
    status: string;
    is_published: boolean;
    teacher_id: string;
    start_date: string;
    end_date: string;
    cover?: File | null;
}

export interface CourseFormErrors {
    title?: string;
    description?: string;
    status?: string;
    is_published?: string;
    teacher_id?: string;
    start_date?: string;
    end_date?: string;
    cover?: string;
}

interface CourseFormProps {
    data: CourseFormData;
    setData: (key: string, value: string | boolean | File | null) => void;
    errors: CourseFormErrors;
    processing: boolean;
    teachers: App.Data.UserData[];
    onSubmit: (e: React.FormEvent) => void;
    submitButtonText: string;
    course?: App.Data.CourseData;
}

export default function CourseForm({ data, setData, errors, processing, teachers, onSubmit, submitButtonText, course }: CourseFormProps) {
    return (
        <Card>
            <CardHeader>
                <CardTitle>Course Information</CardTitle>
            </CardHeader>
            <CardContent>
                <form onSubmit={onSubmit} className="space-y-6">
                    <div className="space-y-2">
                        <Label htmlFor="title">Title</Label>
                        <Input id="title" value={data.title} onChange={(e) => setData('title', e.target.value)} required />
                        {errors.title && <InputError message={errors.title} />}
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="description">Description</Label>
                        <Textarea id="description" value={data.description} onChange={(e) => setData('description', e.target.value)} rows={5} />
                        {errors.description && <InputError message={errors.description} />}
                    </div>

                    <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div className="space-y-2">
                            <Label htmlFor="status">Status</Label>
                            <Select value={data.status} onValueChange={(value) => setData('status', value)}>
                                <SelectTrigger>
                                    <SelectValue placeholder="Select status" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="draft">Draft</SelectItem>
                                    <SelectItem value="active">Active</SelectItem>
                                    <SelectItem value="archived">Archived</SelectItem>
                                </SelectContent>
                            </Select>
                            {errors.status && <InputError message={errors.status} />}
                        </div>

                        <div className="space-y-2">
                            <Label htmlFor="teacher_id">Instructor</Label>
                            <Select value={data.teacher_id} onValueChange={(value) => setData('teacher_id', value)}>
                                <SelectTrigger>
                                    <SelectValue placeholder="Select instructor" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="null">None</SelectItem>
                                    {teachers.length > 0 ? (
                                        teachers.map((teacher) => (
                                            <SelectItem key={teacher.id} value={String(teacher.id)}>
                                                {teacher.name}
                                            </SelectItem>
                                        ))
                                    ) : (
                                        <SelectItem value="0" disabled>
                                            No teachers available
                                        </SelectItem>
                                    )}
                                </SelectContent>
                            </Select>
                            {errors.teacher_id && <InputError message={errors.teacher_id} />}
                        </div>
                    </div>

                    <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div className="space-y-2">
                            <Label htmlFor="start_date">Start Date</Label>
                            <Input id="start_date" type="date" value={data.start_date} onChange={(e) => setData('start_date', e.target.value)} />
                            {errors.start_date && <InputError message={errors.start_date} />}
                        </div>

                        <div className="space-y-2">
                            <Label htmlFor="end_date">End Date</Label>
                            <Input id="end_date" type="date" value={data.end_date} onChange={(e) => setData('end_date', e.target.value)} />
                            {errors.end_date && <InputError message={errors.end_date} />}
                        </div>
                    </div>

                    <div className="space-y-2">
                        <Label>Cover Image</Label>
                        <CoverImageUpload
                            course={course}
                            onFileSelect={(file) => setData('cover', file)}
                            error={errors.cover}
                            processing={processing}
                        />
                    </div>

                    <div className="flex justify-end">
                        <Button type="submit" disabled={processing}>
                            {submitButtonText}
                        </Button>
                    </div>
                </form>
            </CardContent>
        </Card>
    );
}
