import UserForm, { UserFormData } from '@/forms/user-form';
import AppLayout from '@/layouts/app-layout';
import { store } from '@/routes/admin/users';
import { Head, useForm } from '@inertiajs/react';

interface Props {
    availableRoles: App.Data.RoleData[];
}

export default function AdminUserCreate({ availableRoles }: Props) {
    const { post, setData, processing, errors } = useForm<UserFormData>({
        name: '',
        email: '',
        roles: [],
        avatar: null,
    });

    const handleSubmit = (data: UserFormData) => {
        setData(data);
        post(store.url(), {
            preserveScroll: true,
        });
    };

    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Admin', href: '/admin/users' },
                { title: 'Users', href: '/admin/users' },
                { title: 'Add User', href: '#' },
            ]}
        >
            <Head title="Add User" />
            <div className="mx-auto max-w-2xl p-6">
                <div className="mb-6">
                    <h1 className="text-2xl font-bold tracking-tight">
                        Add User
                    </h1>
                    <p className="text-muted-foreground">
                        Create a new user and assign them roles.
                    </p>
                </div>

                <div className="rounded-lg border bg-card p-6 shadow-sm">
                    <UserForm
                        availableRoles={availableRoles}
                        onSubmit={handleSubmit}
                        processing={processing}
                        errors={errors}
                        submitLabel="Create User"
                    />
                </div>
            </div>
        </AppLayout>
    );
}
