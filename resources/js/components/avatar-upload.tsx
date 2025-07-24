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
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { useInitials } from '@/hooks/use-initials';
import { useForm } from '@inertiajs/react';
import { ChangeEvent, DragEvent, useRef, useState } from 'react';

interface AvatarUploadProps {
    user: App.Data.UserData;
}

export default function AvatarUpload({ user }: AvatarUploadProps) {
    const [preview, setPreview] = useState<string | null>(null);
    const [isDragging, setIsDragging] = useState(false);
    const [alertOpen, setAlertOpen] = useState(false);
    const [alertMessage, setAlertMessage] = useState('');
    const fileInputRef = useRef<HTMLInputElement>(null);
    const getInitials = useInitials();

    const {
        data,
        setData,
        post,
        delete: destroy,
        processing,
        errors,
        reset,
    } = useForm({
        avatar: null as File | null,
    });

    const handleFileChange = (e: ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0];
        if (file) {
            validateAndSetFile(file);
        }
    };

    const validateAndSetFile = (file: File) => {
        // Check file size (10MB limit)
        if (file.size > 10 * 1024 * 1024) {
            setAlertMessage('File size must be less than 10MB');
            setAlertOpen(true);
            return;
        }

        // Check file type
        if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
            setAlertMessage('Only JPEG, PNG, and WebP images are allowed');
            setAlertOpen(true);
            return;
        }

        setData('avatar', file);
        setPreview(URL.createObjectURL(file));
    };

    const handleDragOver = (e: DragEvent<HTMLDivElement>) => {
        e.preventDefault();
        setIsDragging(true);
    };

    const handleDragLeave = (e: DragEvent<HTMLDivElement>) => {
        e.preventDefault();
        setIsDragging(false);
    };

    const handleDrop = (e: DragEvent<HTMLDivElement>) => {
        e.preventDefault();
        setIsDragging(false);

        const file = e.dataTransfer.files?.[0];
        if (file) {
            validateAndSetFile(file);
        }
    };

    const handleUpload = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('avatar.update'), {
            preserveScroll: true,
            onSuccess: () => {
                reset('avatar');
                setPreview(null);
            },
        });
    };

    const handleRemove = () => {
        destroy(route('avatar.destroy'), {
            preserveScroll: true,
        });
    };

    const triggerFileInput = () => {
        fileInputRef.current?.click();
    };

    return (
        <div className="space-y-4">
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
                className={`flex flex-col items-center gap-4 rounded-lg border-2 border-dashed p-6 transition-colors ${
                    isDragging ? 'border-primary bg-primary/5' : 'border-neutral-300 dark:border-neutral-700'
                }`}
                onDragOver={handleDragOver}
                onDragLeave={handleDragLeave}
                onDrop={handleDrop}
            >
                <Avatar className="h-24 w-24">
                    <AvatarImage src={preview || user.avatar || undefined} alt={user.name} />
                    <AvatarFallback className="rounded-lg bg-neutral-200 text-4xl text-black dark:bg-neutral-700 dark:text-white">
                        {getInitials(user.name)}
                    </AvatarFallback>
                </Avatar>

                <div className="text-center">
                    <p className="text-muted-foreground mb-2 text-sm">Drag and drop an image here, or click to select one</p>
                    <p className="text-muted-foreground text-xs">JPEG, PNG, WebP • Max 10MB</p>

                    <input
                        type="file"
                        id="avatar"
                        ref={fileInputRef}
                        className="hidden"
                        onChange={handleFileChange}
                        accept="image/jpeg,image/png,image/webp"
                    />

                    <Button type="button" variant="outline" onClick={triggerFileInput} className="mt-4">
                        Select Image
                    </Button>
                </div>
            </div>

            {errors.avatar && <InputError message={errors.avatar} />}

            <div className="flex items-center gap-4">
                {data.avatar && (
                    <Button type="button" onClick={handleUpload} disabled={processing}>
                        Upload Avatar
                    </Button>
                )}

                {user.avatar && (
                    <Button type="button" variant="destructive" onClick={handleRemove} disabled={processing}>
                        Remove Avatar
                    </Button>
                )}
            </div>
        </div>
    );
}
