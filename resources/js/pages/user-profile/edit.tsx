import { AppMediaInput, type MediaData } from '@/components/app-media-input';
import { send } from '@/routes/verification';
import { type BreadcrumbItem, type SharedData } from '@/types';
import { Transition } from '@headlessui/react';
import { Form, Head, Link, usePage } from '@inertiajs/react';

import DeleteUser from '@/components/delete-user';
import HeadingSmall from '@/components/heading-small';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/app-layout';
import SettingsLayout from '@/layouts/settings/layout';
import userProfile from '@/routes/user-profile';
import { useMemo, useState } from 'react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Profile settings',
        href: userProfile.edit().url,
    },
];

export default function Edit({ status }: { status?: string }) {
    const { auth } = usePage<SharedData>().props;
    const [avatarMedia, setAvatarMedia] = useState<MediaData | null>(null);
    const [removeAvatar, setRemoveAvatar] = useState(false);

    const initialAvatar = useMemo(() => {
        if (removeAvatar || avatarMedia) {
            return undefined;
        }

        return auth.user.avatar
            ? { preview: auth.user.avatar, name: auth.user.name }
            : undefined;
    }, [auth.user.avatar, auth.user.name, avatarMedia, removeAvatar]);

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Profile settings" />

            <SettingsLayout>
                <div className="space-y-6">
                    <HeadingSmall
                        title="Profile information"
                        description="Update your name and email address"
                    />

                    <Form
                        action={userProfile.update().url}
                        method="patch"
                        options={{
                            preserveScroll: true,
                        }}
                        className="space-y-6"
                    >
                        {({ processing, recentlySuccessful, errors }) => (
                            <>
                                <div className="grid gap-2">
                                    <Label htmlFor="name">Name</Label>

                                    <Input
                                        id="name"
                                        className="mt-1 block w-full"
                                        defaultValue={auth.user.name}
                                        name="name"
                                        required
                                        autoComplete="name"
                                        placeholder="Full name"
                                    />

                                    <InputError
                                        className="mt-2"
                                        message={errors.name}
                                    />
                                </div>

                                <div className="grid gap-2">
                                    <Label htmlFor="email">Email address</Label>

                                    <Input
                                        id="email"
                                        type="email"
                                        className="mt-1 block w-full"
                                        defaultValue={auth.user.email}
                                        name="email"
                                        required
                                        autoComplete="username"
                                        placeholder="Email address"
                                    />

                                    <InputError
                                        className="mt-2"
                                        message={errors.email}
                                    />
                                </div>

                                <div className="grid gap-2">
                                    <Label>Avatar</Label>
                                    <AppMediaInput
                                        label="Upload avatar"
                                        display="minimal"
                                        type="images"
                                        maxFileSize={5 * 1024 * 1024}
                                        backgroundUpload
                                        value={initialAvatar}
                                        onChange={(file) => {
                                            if (!file) {
                                                setAvatarMedia(null);
                                                setRemoveAvatar(true);
                                                return;
                                            }

                                            if (Array.isArray(file)) {
                                                const media =
                                                    (file[0] as
                                                        | MediaData
                                                        | undefined) ?? null;
                                                setAvatarMedia(media);
                                                setRemoveAvatar(media === null);

                                                return;
                                            }

                                            setAvatarMedia(file as MediaData);
                                            setRemoveAvatar(false);
                                        }}
                                    />
                                    <p className="text-xs text-muted-foreground">
                                        Images only, up to 5MB. Changes apply
                                        when you save.
                                    </p>
                                    <input
                                        type="hidden"
                                        name="avatar_media_id"
                                        value={avatarMedia?.id ?? ''}
                                    />
                                    <input
                                        type="hidden"
                                        name="remove_avatar"
                                        value={removeAvatar ? '1' : '0'}
                                    />
                                </div>

                                {auth.user.emailVerifiedAt === null && (
                                    <div>
                                        <p className="-mt-4 text-sm text-muted-foreground">
                                            Your email address is unverified.{' '}
                                            <Link
                                                href={send()}
                                                as="button"
                                                className="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                                            >
                                                Click here to resend the
                                                verification email.
                                            </Link>
                                        </p>

                                        {status ===
                                            'verification-link-sent' && (
                                            <div className="mt-2 text-sm font-medium text-green-600">
                                                A new verification link has been
                                                sent to your email address.
                                            </div>
                                        )}
                                    </div>
                                )}

                                <div className="flex items-center gap-4">
                                    <Button
                                        disabled={processing}
                                        data-test="update-profile-button"
                                    >
                                        Save
                                    </Button>

                                    <Transition
                                        show={recentlySuccessful}
                                        enter="transition ease-in-out"
                                        enterFrom="opacity-0"
                                        leave="transition ease-in-out"
                                        leaveTo="opacity-0"
                                    >
                                        <p className="text-sm text-neutral-600">
                                            Saved
                                        </p>
                                    </Transition>
                                </div>
                            </>
                        )}
                    </Form>
                </div>

                <DeleteUser />
            </SettingsLayout>
        </AppLayout>
    );
}
