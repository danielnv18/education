import UserForm from '@/components/admin/user-form';
import HeadingLarge from '@/components/heading-large';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import { User } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { ArrowLeftIcon } from 'lucide-react';

interface UserEditPageProps {
    user: User;
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
            href: route('admin.users.edit', user.id),
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Edit User - ${user.name}`} />

            <div className="space-y-6 p-4">
                <div className="flex items-center justify-between">
                    <HeadingLarge title="Edit User" description={`Update information for ${user.name}`} />
                    <Button variant="outline" asChild>
                        <Link href={route('admin.users.index')}>
                            <ArrowLeftIcon className="mr-2 h-4 w-4" />
                            Back to Users
                        </Link>
                    </Button>
                </div>

                <div className="rounded-lg border p-6">
                    <UserForm user={user} action={route('admin.users.update', user.id)} />
                </div>
            </div>
        </AppLayout>
    );
}
