import UserTable from '@/components/admin/user-table';
import HeadingLarge from '@/components/heading-large';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import { PageProps, User } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { PlusIcon } from 'lucide-react';

interface UserIndexPageProps extends PageProps {
    users: {
        data: User[];
        links: { url: string | null; label: string; active: boolean }[];
        from: number;
        to: number;
        total: number;
    };
}

const breadcrumbs = [
    {
        title: 'Dashboard',
        href: route('dashboard'),
    },
    {
        title: 'Users',
        href: route('admin.users.index'),
    },
];

export default function UserIndexPage({ users }: UserIndexPageProps) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Users" />

            <div className="space-y-6 p-4">
                <div className="flex items-center justify-between">
                    <HeadingLarge title="Users" description="Manage user accounts" />
                    <Button asChild>
                        <Link href={route('admin.users.create')}>
                            <PlusIcon className="mr-2 h-4 w-4" />
                            Add User
                        </Link>
                    </Button>
                </div>

                <UserTable users={users} />
            </div>
        </AppLayout>
    );
}
