import InputError from '@/components/input-error';
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog';
import { Button } from '@/components/ui/button';
import { ChangeEvent, DragEvent, ReactNode, useRef, useState } from 'react';

export interface FileValidationOptions {
    maxSize?: number; // in bytes
    acceptedTypes?: string[];
    customValidator?: (file: File) => { valid: boolean; message?: string } | boolean;
}

export interface FileUploadProps {
    onFileSelect: (file: File) => void;
    onFileError?: (message: string) => void;
    validation?: FileValidationOptions;
    error?: string;
    children?: ReactNode;
    buttonText?: string;
    dragActiveText?: string;
    dragInactiveText?: string;
    acceptedTypesText?: string;
    className?: string;
    disabled?: boolean;
    compact?: boolean;
    hideText?: boolean;
    size?: 'sm' | 'md' | 'lg';
}

export default function FileUpload({
    onFileSelect,
    onFileError,
    validation = {},
    error,
    children,
    buttonText = 'Select File',
    dragActiveText = 'Drop file here',
    dragInactiveText = 'Drag and drop a file here, or click to select one',
    acceptedTypesText,
    className = '',
    disabled = false,
    compact = false,
    hideText = false,
    size = 'md',
}: FileUploadProps) {
    const [isDragging, setIsDragging] = useState(false);
    const [alertOpen, setAlertOpen] = useState(false);
    const [alertMessage, setAlertMessage] = useState('');
    const fileInputRef = useRef<HTMLInputElement>(null);

    const {
        maxSize = 10 * 1024 * 1024, // Default 10MB
        acceptedTypes = [],
        customValidator,
    } = validation;

    const acceptAttribute = acceptedTypes.length > 0 ? acceptedTypes.join(',') : undefined;

    const validateFile = (file: File): boolean => {
        // Check file size
        if (maxSize && file.size > maxSize) {
            const sizeMB = Math.round(maxSize / (1024 * 1024));
            const errorMessage = `File size must be less than ${sizeMB}MB`;
            setAlertMessage(errorMessage);
            setAlertOpen(true);
            onFileError?.(errorMessage);
            return false;
        }

        // Check file type
        if (
            acceptedTypes.length > 0 &&
            !acceptedTypes.some((type) => {
                // Handle mime types with wildcards like "image/*"
                if (type.endsWith('/*')) {
                    const category = type.split('/')[0];
                    return file.type.startsWith(`${category}/`);
                }
                return file.type === type;
            })
        ) {
            const typesText = acceptedTypes.map((type) => type.replace('/*', '')).join(', ');
            const errorMessage = `Only ${typesText} files are allowed`;
            setAlertMessage(errorMessage);
            setAlertOpen(true);
            onFileError?.(errorMessage);
            return false;
        }

        // Run custom validator if provided
        if (customValidator) {
            const result = customValidator(file);
            if (typeof result === 'boolean') {
                if (!result) {
                    const errorMessage = 'File validation failed';
                    setAlertMessage(errorMessage);
                    setAlertOpen(true);
                    onFileError?.(errorMessage);
                    return false;
                }
            } else if (!result.valid) {
                const errorMessage = result.message || 'File validation failed';
                setAlertMessage(errorMessage);
                setAlertOpen(true);
                onFileError?.(errorMessage);
                return false;
            }
        }

        return true;
    };

    const handleFileChange = (e: ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0];
        if (file && !disabled) {
            if (validateFile(file)) {
                onFileSelect(file);
            }
        }
    };

    const handleDragOver = (e: DragEvent<HTMLDivElement>) => {
        e.preventDefault();
        if (!disabled) {
            setIsDragging(true);
        }
    };

    const handleDragLeave = (e: DragEvent<HTMLDivElement>) => {
        e.preventDefault();
        setIsDragging(false);
    };

    const handleDrop = (e: DragEvent<HTMLDivElement>) => {
        e.preventDefault();
        setIsDragging(false);

        if (disabled) return;

        const file = e.dataTransfer.files?.[0];
        if (file) {
            if (validateFile(file)) {
                onFileSelect(file);
            }
        }
    };

    const triggerFileInput = () => {
        if (!disabled) {
            fileInputRef.current?.click();
        }
    };

    // Determine size-based classes
    const sizeClasses = {
        sm: {
            container: 'gap-2 p-3',
            button: 'text-xs h-8 px-3',
            spacing: 'space-y-2',
        },
        md: {
            container: 'gap-3 p-4',
            button: 'text-sm',
            spacing: 'space-y-3',
        },
        lg: {
            container: 'gap-4 p-6',
            button: 'text-base',
            spacing: 'space-y-4',
        },
    }[size];

    // Apply compact mode adjustments
    const compactClasses = compact ? 'flex-col sm:flex-row items-center sm:justify-between' : 'flex-col items-center';
    const compactTextClasses = compact ? 'text-center sm:text-left' : 'text-center';
    const compactButtonMargin = compact ? 'mt-2 sm:mt-0' : 'mt-3';

    // Spacing between components
    const containerSpacing = compact ? 'space-y-2' : sizeClasses.spacing;

    return (
        <div className={`${containerSpacing} ${className}`}>
            <AlertDialog open={alertOpen} onOpenChange={setAlertOpen}>
                <AlertDialogContent>
                    <AlertDialogHeader>
                        <AlertDialogTitle>Invalid File</AlertDialogTitle>
                        <AlertDialogDescription>{alertMessage}</AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                        <AlertDialogAction onClick={() => setAlertOpen(false)}>OK</AlertDialogAction>
                    </AlertDialogFooter>
                </AlertDialogContent>
            </AlertDialog>

            <div
                className={`flex ${compactClasses} rounded-lg border-2 border-dashed ${sizeClasses.container} transition-colors ${
                    disabled ? 'cursor-not-allowed opacity-50' : 'cursor-pointer'
                } ${isDragging ? 'border-primary bg-primary/5' : 'border-neutral-300 dark:border-neutral-700'} w-full`}
                onDragOver={handleDragOver}
                onDragLeave={handleDragLeave}
                onDrop={handleDrop}
                onClick={triggerFileInput}
            >
                {children}

                <div className={`${compactTextClasses} ${compact ? 'mx-3 flex-1' : 'w-full'}`}>
                    {!hideText && (
                        <>
                            <p className="text-muted-foreground mb-1 text-sm">{isDragging ? dragActiveText : dragInactiveText}</p>
                            {acceptedTypesText && <p className="text-muted-foreground text-xs">{acceptedTypesText}</p>}
                        </>
                    )}

                    <input
                        type="file"
                        ref={fileInputRef}
                        className="hidden"
                        onChange={handleFileChange}
                        accept={acceptAttribute}
                        disabled={disabled}
                    />

                    <Button
                        type="button"
                        variant="outline"
                        onClick={(e) => {
                            e.stopPropagation();
                            triggerFileInput();
                        }}
                        className={`${compactButtonMargin} ${sizeClasses.button}`}
                        disabled={disabled}
                        size={size === 'sm' ? 'sm' : size === 'lg' ? 'lg' : 'default'}
                    >
                        {buttonText}
                    </Button>
                </div>
            </div>

            {error && <InputError message={error} />}
        </div>
    );
}
