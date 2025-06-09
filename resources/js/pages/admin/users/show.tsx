import ResetPasswordButton from '@/components/admin/reset-password-button';
import HeadingLarge from '@/components/heading-large';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import { User } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { ArrowLeftIcon, PencilIcon } from 'lucide-react';

interface UserShowPageProps {
    user: User;
}

export default function UserShowPage({ user }: UserShowPageProps) {
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
            title: user.name,
            href: route('admin.users.show', user.id),
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`User - ${user.name}`} />

            <div className="space-y-6 p-4">
                <div className="flex items-center justify-between">
                    <HeadingLarge title={user.name} description="User details" />
                    <div className="flex space-x-2">
                        <Button variant="outline" asChild>
                            <Link href={route('admin.users.index')}>
                                <ArrowLeftIcon className="mr-2 h-4 w-4" />
                                Back to Users
                            </Link>
                        </Button>
                        <ResetPasswordButton user={user} />
                        <Button asChild>
                            <Link href={route('admin.users.edit', user.id)}>
                                <PencilIcon className="mr-2 h-4 w-4" />
                                Edit User
                            </Link>
                        </Button>
                    </div>
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle>User Information</CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        <div>
                            <h3 className="text-sm font-medium text-gray-500 dark:text-gray-400">Email</h3>
                            <p>{user.email}</p>
                        </div>

                        <div>
                            <h3 className="text-sm font-medium text-gray-500 dark:text-gray-400">Roles</h3>
                            <div className="mt-1 flex flex-wrap gap-1">
                                {user.roles.map((role) => (
                                    <Badge key={typeof role === 'object' ? role.name : role} variant="outline">
                                        {typeof role === 'object' ? role.name : role}
                                    </Badge>
                                ))}
                            </div>
                        </div>

                        <div>
                            <h3 className="text-sm font-medium text-gray-500 dark:text-gray-400">Account Created</h3>
                            <p>{new Date(user.created_at).toLocaleDateString()}</p>
                        </div>

                        <div>
                            <h3 className="text-sm font-medium text-gray-500 dark:text-gray-400">Email Verification</h3>
                            <p>{user.email_verified_at ? 'Verified' : 'Not verified'}</p>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
