import ResetPasswordButton from '@/components/admin/reset-password-button';
import UserForm from '@/components/admin/user-form';
import HeadingLarge from '@/components/heading-large';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import { Head, Link } from '@inertiajs/react';
import { ArrowLeftIcon } from 'lucide-react';

interface UserEditPageProps {
    user: App.Data.UserData;
}

export default function UserEditPage({ user }: UserEditPageProps) {
    const breadcrumbs = [
        {
            title: 'Dashboard',
            href: route('dashboard'),
        },
        {
            title: 'Users',
            href: route('admin.users.index'),
        },
        {
            title: 'Edit',
            href: route('admin.users.edit', { user: user.id }),
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Edit User - ${user.name}`} />

            <div className="space-y-6 p-4">
                <div className="flex items-center justify-between">
                    <HeadingLarge title="Edit User" description={`Update information for ${user.name}`} />
                    <div className="flex flex-row gap-2">
                        <Button variant="outline" asChild>
                            <Link href={route('admin.users.index')}>
                                <ArrowLeftIcon className="mr-2 h-4 w-4" />
                                Back to Users
                            </Link>
                        </Button>
                        <ResetPasswordButton user={user} />
                    </div>
                </div>

                <div className="rounded-lg border p-6">
                    <UserForm user={user} action={route('admin.users.update', { user: user.id })} />
                </div>
            </div>
        </AppLayout>
    );
}
