import UserForm, { UserFormData } from '@/forms/user-form';
import AppLayout from '@/layouts/app-layout';
import { update } from '@/routes/admin/users';
import { Head, useForm } from '@inertiajs/react';

interface Props {
    user: App.Data.UserData;
    availableRoles: App.Data.RoleData[];
}

export default function AdminUserEdit({ user, availableRoles }: Props) {
    const { patch, setData, processing, errors } = useForm<UserFormData>({
        name: user.name,
        email: user.email,
        roles: user.roles.map((r) => r.name),
        avatar: user.avatar,
    });

    const handleSubmit = (data: UserFormData) => {
        setData(data);
        patch(update.url(user.id), {
            preserveScroll: true,
        });
    };

    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Admin', href: '/admin/users' },
                { title: 'Users', href: '/admin/users' },
                { title: 'Edit User', href: '#' },
            ]}
        >
            <Head title={`Edit User: ${user.name}`} />
            <div className="mx-auto max-w-2xl p-6">
                <div className="mb-6">
                    <h1 className="text-2xl font-bold tracking-tight">
                        Edit User
                    </h1>
                    <p className="text-muted-foreground">
                        Update user details and roles.
                    </p>
                </div>

                <div className="rounded-lg border bg-card p-6 shadow-sm">
                    <UserForm
                        initialData={{
                            name: user.name,
                            email: user.email,
                            roles: user.roles.map((r) => r.name),
                            avatar: user.avatar,
                        }}
                        availableRoles={availableRoles}
                        onSubmit={handleSubmit}
                        processing={processing}
                        errors={errors}
                        submitLabel="Update User"
                    />
                </div>
            </div>
        </AppLayout>
    );
}
