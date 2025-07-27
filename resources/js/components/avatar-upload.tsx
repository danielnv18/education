import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { useInitials } from '@/hooks/use-initials';
import { useForm } from '@inertiajs/react';
import { useState } from 'react';
import FileUpload from './file-upload';

interface AvatarUploadProps {
    user: App.Data.UserData;
}

export default function AvatarUpload({ user }: AvatarUploadProps) {
    const [preview, setPreview] = useState<string | null>(null);
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

    const handleFileSelect = (file: File) => {
        setData('avatar', file);
        setPreview(URL.createObjectURL(file));
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

    return (
        <div className="space-y-3">
            <FileUpload
                onFileSelect={handleFileSelect}
                validation={{
                    maxSize: 10 * 1024 * 1024, // 10MB
                    acceptedTypes: ['image/jpeg', 'image/png', 'image/webp'],
                }}
                error={errors.avatar}
                buttonText="Select"
                dragInactiveText="Drop image or click to browse"
                acceptedTypesText="JPEG, PNG, WebP • Max 10MB"
                disabled={processing}
                compact={true}
                size="sm"
                className="w-full"
            >
                <Avatar className="mx-auto mb-2 h-14 w-14 shrink-0 sm:mx-0 sm:mb-0 sm:h-16 sm:w-16">
                    <AvatarImage src={preview || user.avatar || undefined} alt={user.name} />
                    <AvatarFallback className="rounded-lg bg-neutral-200 text-base text-black sm:text-lg dark:bg-neutral-700 dark:text-white">
                        {getInitials(user.name)}
                    </AvatarFallback>
                </Avatar>
            </FileUpload>

            <div className="flex flex-wrap items-center gap-2 sm:gap-3">
                {data.avatar && (
                    <Button type="button" onClick={handleUpload} disabled={processing} size="sm" className="flex-1 sm:flex-none">
                        Upload Avatar
                    </Button>
                )}

                {user.avatar && (
                    <Button
                        type="button"
                        variant="destructive"
                        onClick={handleRemove}
                        disabled={processing}
                        size="sm"
                        className="flex-1 sm:flex-none"
                    >
                        Remove Avatar
                    </Button>
                )}
            </div>
        </div>
    );
}
