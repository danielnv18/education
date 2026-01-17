import { cn } from '@/lib/utils';
import {
    FileCode,
    FileIcon,
    FileSpreadsheet,
    FileText,
    ImageIcon,
    Presentation,
    UploadCloud,
    X,
} from 'lucide-react';
import { useCallback, useEffect, useRef, useState } from 'react';

export type AppMediaInputType = 'all' | 'images' | 'documents';
export type AppMediaInputDisplay = 'minimal' | 'medium' | 'full';

export interface AppMediaInputFile {
    id: string;
    file: File | null;
    preview: string | null;
    name: string;
    size: number;
    type: string;
}

export interface AppMediaInputInitialFile {
    preview?: string;
    name?: string;
    size?: number;
    type?: string;
}

export interface AppMediaInputProps {
    name?: string;
    label?: string;
    value?:
        | File
        | File[]
        | AppMediaInputInitialFile
        | AppMediaInputInitialFile[];
    multiple?: boolean;
    minFiles?: number;
    maxFiles?: number;
    maxFileSize?: number;
    type?: AppMediaInputType;
    display?: AppMediaInputDisplay;
    backgroundUpload?: boolean;
    onChange?: (files: File | File[] | null) => void;
    className?: string;
}

const FILE_TYPES: Record<AppMediaInputType, string> = {
    all: '*',
    images: 'image/jpeg,image/png,image/gif,image/webp,image/svg+xml,image/avif',
    documents: '.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.odt,.ods,.odp',
};

const DEFAULT_MAX_SIZE = 10 * 1024 * 1024; // 10MB

export function AppMediaInput({
    name,
    label,
    value,
    multiple = false,
    minFiles = 2,
    maxFiles = 10,
    maxFileSize = DEFAULT_MAX_SIZE,
    type = 'all',
    display = 'medium',
    backgroundUpload = false,
    onChange,
    className,
}: AppMediaInputProps) {
    const [files, setFiles] = useState<AppMediaInputFile[]>([]);
    const [isDragging, setIsDragging] = useState(false);
    const inputRef = useRef<HTMLInputElement>(null);

    // Sync internal state with prop value if provided
    useEffect(() => {
        if (value) {
            const initialValues = Array.isArray(value) ? value : [value];
            setFiles(
                initialValues.map((v) => {
                    const isFile = v instanceof File;
                    return {
                        id: Math.random().toString(36).substring(7),
                        file: isFile ? v : null,
                        preview: isFile
                            ? URL.createObjectURL(v)
                            : (v as AppMediaInputInitialFile).preview || null,
                        name: v.name || 'Unknown File',
                        size: v.size || 0,
                        type: v.type || '',
                    };
                }),
            );
        }
    }, [value]);

    const validateFile = useCallback(
        (file: File) => {
            if (file.size > maxFileSize) {
                alert(
                    `File ${file.name} is too large. Maximum size is ${maxFileSize / (1024 * 1024)}MB.`,
                );
                return false;
            }

            if (type === 'images' && !file.type.startsWith('image/')) {
                alert(`File ${file.name} is not an image.`);
                return false;
            }

            if (type === 'documents') {
                const docExtensions = FILE_TYPES.documents.split(',');
                const ext = `.${file.name.split('.').pop()?.toLowerCase()}`;
                if (
                    !docExtensions.includes(ext) &&
                    !file.type.includes('pdf') &&
                    !file.type.includes('word') &&
                    !file.type.includes('spreadsheet')
                ) {
                    alert(
                        `File ${file.name} is not a supported document type.`,
                    );
                    return false;
                }
            }

            return true;
        },
        [maxFileSize, type],
    );

    const handleFiles = useCallback(
        (newFiles: FileList | null) => {
            if (!newFiles) return;

            const validFiles = Array.from(newFiles).filter(validateFile);

            if (validFiles.length === 0) return;

            let updatedFiles: AppMediaInputFile[];
            if (multiple) {
                updatedFiles = [
                    ...files,
                    ...validFiles.map((f) => ({
                        id: Math.random().toString(36).substring(7),
                        file: f,
                        preview: f.type.startsWith('image/')
                            ? URL.createObjectURL(f)
                            : null,
                        name: f.name,
                        size: f.size,
                        type: f.type,
                    })),
                ];
            } else {
                updatedFiles = [
                    {
                        id: Math.random().toString(36).substring(7),
                        file: validFiles[0],
                        preview: validFiles[0].type.startsWith('image/')
                            ? URL.createObjectURL(validFiles[0])
                            : null,
                        name: validFiles[0].name,
                        size: validFiles[0].size,
                        type: validFiles[0].type,
                    },
                ];
            }

            setFiles(updatedFiles);

            if (onChange) {
                onChange(
                    multiple
                        ? updatedFiles
                              .map((f) => f.file)
                              .filter((f): f is File => f !== null)
                        : updatedFiles[0]?.file || null,
                );
            }

            // Background upload logic would go here if enabled
            if (backgroundUpload) {
                console.log('Simulating background upload for:', validFiles);
            }
        },
        [files, multiple, onChange, backgroundUpload, validateFile],
    );

    const onDragOver = (e: React.DragEvent) => {
        e.preventDefault();
        setIsDragging(true);
    };

    const onDragLeave = () => {
        setIsDragging(false);
    };

    const onDrop = (e: React.DragEvent) => {
        e.preventDefault();
        setIsDragging(false);
        handleFiles(e.dataTransfer.files);
    };

    const removeFile = (id: string) => {
        const updatedFiles = files.filter((f) => f.id !== id);
        setFiles(updatedFiles);
        if (onChange) {
            onChange(
                multiple
                    ? updatedFiles
                          .map((f) => f.file)
                          .filter((f): f is File => f !== null)
                    : updatedFiles[0]?.file || null,
            );
        }
    };

    const getFileIcon = (file: AppMediaInputFile) => {
        const type = file.type || '';
        const name = file.name || '';
        if (type.startsWith('image/'))
            return <ImageIcon className="h-8 w-8 text-blue-500" />;
        if (type.includes('pdf'))
            return <FileText className="h-8 w-8 text-red-500" />;
        if (
            type.includes('spreadsheet') ||
            name.endsWith('.csv') ||
            name.endsWith('.xlsx')
        )
            return <FileSpreadsheet className="h-8 w-8 text-green-500" />;
        if (type.includes('presentation') || name.endsWith('.pptx'))
            return <Presentation className="h-8 w-8 text-orange-500" />;
        if (
            type.includes('code') ||
            name.endsWith('.js') ||
            name.endsWith('.php')
        )
            return <FileCode className="h-8 w-8 text-purple-500" />;
        return <FileIcon className="h-8 w-8 text-gray-500" />;
    };

    const formatSize = (bytes: number) => {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    };

    return (
        <div className={cn('w-full space-y-2', className)}>
            {label && (
                <label className="text-sm font-medium text-gray-700 dark:text-gray-300">
                    {label}
                </label>
            )}

            <div
                onDragOver={onDragOver}
                onDragLeave={onDragLeave}
                onDrop={onDrop}
                onClick={() => inputRef.current?.click()}
                className={cn(
                    'relative flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed text-center transition-all duration-200',
                    isDragging
                        ? 'scale-[1.01] border-primary bg-primary/5'
                        : 'border-gray-300 hover:border-primary/50 dark:border-gray-700',
                    display === 'minimal'
                        ? 'min-h-[6rem] p-4'
                        : display === 'medium'
                          ? 'min-h-[10rem] p-6 md:p-8'
                          : 'min-h-[16rem] p-8 md:p-12',
                    files.length > 0 && 'hidden',
                )}
            >
                <input
                    ref={inputRef}
                    type="file"
                    name={name}
                    multiple={multiple}
                    accept={FILE_TYPES[type]}
                    onChange={(e) => handleFiles(e.target.files)}
                    className="hidden"
                />

                <div className="flex flex-col items-center gap-2">
                    <div className="rounded-full bg-gray-100 p-3 dark:bg-gray-800">
                        <UploadCloud className="h-6 w-6 text-gray-500" />
                    </div>
                    {display !== 'minimal' && (
                        <div>
                            <p className="text-sm font-semibold">
                                Click to upload or drag and drop
                            </p>
                            <p className="mt-1 text-xs text-gray-500">
                                {type === 'images'
                                    ? 'SVG, PNG, JPG or GIF'
                                    : type === 'documents'
                                      ? 'PDF, DOC, XLS or PPT'
                                      : 'Any file'}{' '}
                                (max {maxFileSize / (1024 * 1024)}MB)
                            </p>
                        </div>
                    )}
                </div>
            </div>

            {/* Previews */}
            {files.length > 0 && (
                <div
                    className={cn(
                        'mt-4 grid gap-4',
                        display === 'minimal' || !multiple
                            ? 'grid-cols-1'
                            : 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3',
                        'w-full',
                    )}
                >
                    {files.map((file) => (
                        <div
                            key={file.id}
                            className="group relative flex h-full min-h-[72px] items-center gap-3 overflow-hidden rounded-lg border border-gray-200 bg-white p-3 shadow-sm transition-shadow hover:shadow-md dark:border-gray-800 dark:bg-gray-900"
                        >
                            <div className="flex h-12 w-12 flex-shrink-0 items-center justify-center overflow-hidden rounded bg-gray-50 dark:bg-gray-800">
                                {file.preview ? (
                                    <img
                                        src={file.preview}
                                        alt={file.name}
                                        className="h-full w-full object-cover"
                                    />
                                ) : (
                                    getFileIcon(file)
                                )}
                            </div>

                            <div className="min-w-0 flex-1">
                                <p className="truncate pr-6 text-sm font-medium">
                                    {file.name}
                                </p>
                                <p className="text-xs text-gray-500">
                                    {formatSize(file.size)}
                                </p>
                            </div>

                            <button
                                onClick={(e) => {
                                    e.stopPropagation();
                                    removeFile(file.id);
                                }}
                                className="absolute top-2 right-2 rounded-full bg-gray-100 p-1 opacity-0 shadow-sm transition-all group-hover:opacity-100 hover:bg-red-100 hover:text-red-500 dark:bg-gray-800 dark:hover:bg-red-900/30"
                            >
                                <X className="h-3.5 w-3.5" />
                            </button>
                        </div>
                    ))}

                    {multiple && files.length < maxFiles && (
                        <AddMoreButton
                            onClick={() => inputRef.current?.click()}
                            onDrop={handleFiles}
                        />
                    )}
                </div>
            )}

            {multiple && files.length > 0 && files.length < minFiles && (
                <p className="mt-2 text-xs text-red-500">
                    Please select at least {minFiles} files.
                </p>
            )}
        </div>
    );
}

function AddMoreButton({
    onClick,
    onDrop,
}: {
    onClick: () => void;
    onDrop: (files: FileList | null) => void;
}) {
    const [isDragging, setIsDragging] = useState(false);

    const handleDragOver = (e: React.DragEvent) => {
        e.preventDefault();
        e.stopPropagation();
        setIsDragging(true);
    };

    const handleDragLeave = (e: React.DragEvent) => {
        e.preventDefault();
        e.stopPropagation();
        setIsDragging(false);
    };

    const handleDrop = (e: React.DragEvent) => {
        e.preventDefault();
        e.stopPropagation();
        setIsDragging(false);
        if (e.dataTransfer.files) {
            onDrop(e.dataTransfer.files);
        }
    };

    return (
        <button
            type="button"
            onClick={onClick}
            onDragOver={handleDragOver}
            onDragLeave={handleDragLeave}
            onDrop={handleDrop}
            className={cn(
                'flex h-full min-h-[72px] items-center justify-center rounded-lg border-2 border-dashed p-3 transition-all duration-200',
                isDragging
                    ? 'scale-[1.02] border-primary bg-primary/5'
                    : 'border-gray-200 hover:border-primary/50 dark:border-gray-700',
            )}
        >
            <UploadCloud
                className={cn(
                    'mr-2 h-5 w-5 transition-colors',
                    isDragging ? 'text-primary' : 'text-gray-400',
                )}
            />
            <span
                className={cn(
                    'text-sm transition-colors',
                    isDragging ? 'font-medium text-primary' : 'text-gray-500',
                )}
            >
                {isDragging ? 'Drop to add' : 'Add more'}
            </span>
        </button>
    );
}
