import UserForm from '@/components/admin/user-form';
import HeadingLarge from '@/components/heading-large';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import { Head, Link } from '@inertiajs/react';
import { ArrowLeftIcon } from 'lucide-react';

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
        title: 'Create',
        href: route('admin.users.create'),
    },
];

export default function UserCreatePage() {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Create User" />

            <div className="space-y-6 p-4">
                <div className="flex items-center justify-between">
                    <HeadingLarge title="Create User" description="Add a new user to the system" />
                    <Button variant="outline" asChild>
                        <Link href={route('admin.users.index')}>
                            <ArrowLeftIcon className="mr-2 h-4 w-4" />
                            Back to Users
                        </Link>
                    </Button>
                </div>

                <div className="rounded-lg border p-6">
                    <UserForm action={route('admin.users.store')} />
                </div>
            </div>
        </AppLayout>
    );
}
