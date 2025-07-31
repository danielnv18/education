import { Button } from '@/components/ui/button';
import { useState } from 'react';
import FileUpload from './file-upload';

interface CoverImageUploadProps {
    course?: App.Data.CourseData;
    onFileSelect: (file: File | null) => void;
    error?: string;
    processing?: boolean;
}

export default function CoverImageUpload({ course, onFileSelect, error, processing = false }: CoverImageUploadProps) {
    const [preview, setPreview] = useState<string | null>(null);
    const [removed, setRemoved] = useState(false);

    const handleFileSelect = (file: File) => {
        onFileSelect(file);
        setPreview(URL.createObjectURL(file));
        setRemoved(false);
    };

    const handleRemove = () => {
        onFileSelect(null);
        setPreview(null);
        setRemoved(true);
    };

    // Determine if we should show an image
    // Only show the image if we have a preview or the course has a cover and it hasn't been removed
    const showImage = preview || (course?.cover && !removed);

    return (
        <div className="space-y-3">
            <FileUpload
                onFileSelect={handleFileSelect}
                validation={{
                    maxSize: 10 * 1024 * 1024, // 10MB
                    acceptedTypes: ['image/jpeg', 'image/png', 'image/webp'],
                }}
                error={error}
                buttonText="Select"
                dragInactiveText="Drop cover image or click to browse"
                acceptedTypesText="JPEG, PNG, WebP • Max 10MB"
                disabled={processing}
                compact={true}
                size="sm"
                className="w-full"
            >
                <div className="relative mx-auto mb-2 h-32 w-48 shrink-0 overflow-hidden rounded-md border border-neutral-200 bg-neutral-100 sm:mx-0 sm:mb-0 dark:border-neutral-700 dark:bg-neutral-800">
                    {showImage && (
                        <img src={preview || (course && course.cover) || undefined} alt="Course cover" className="h-full w-full object-cover" />
                    )}
                    {!showImage && (
                        <div className="flex h-full w-full flex-col items-center justify-center bg-neutral-100 p-2 text-center dark:bg-neutral-800">
                            {course?.title ? (
                                <>
                                    <div className="text-xs font-medium text-neutral-500 dark:text-neutral-400">{course.title}</div>
                                    <div className="mt-1 text-[10px] text-neutral-400 dark:text-neutral-500">No cover image</div>
                                </>
                            ) : (
                                <span className="text-xs text-neutral-400">Course title will appear here</span>
                            )}
                        </div>
                    )}
                </div>
            </FileUpload>

            {showImage && (
                <Button type="button" variant="destructive" onClick={handleRemove} disabled={processing} size="sm">
                    Remove Cover Image
                </Button>
            )}
        </div>
    );
}
