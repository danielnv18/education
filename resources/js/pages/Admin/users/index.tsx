import { AppDataTable } from '@/components/app-datatable';
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import AppLayout from '@/layouts/app-layout';
import { create, destroy, edit } from '@/routes/admin/users';
import { Head, Link, useForm } from '@inertiajs/react';
import { type ColumnDef } from '@tanstack/react-table';
import { format, parseISO } from 'date-fns';
import { Check, Clipboard, MoreHorizontal, Pencil, Trash2 } from 'lucide-react';
import { useState } from 'react';
import { toast } from 'sonner';

interface Props {
    users: App.Data.UserData[];
}

export default function AdminUsersIndex({ users }: Props) {
    const [copiedId, setCopiedId] = useState<number | null>(null);
    const [userToDelete, setUserToDelete] = useState<App.Data.UserData | null>(
        null,
    );

    const { delete: destroyUser, processing } = useForm({});

    const handleDelete = () => {
        if (!userToDelete) return;

        destroyUser(destroy.url(userToDelete.id), {
            onSuccess: () => {
                toast.success('User deleted successfully');
                setUserToDelete(null);
            },
            onError: () => {
                toast.error('Failed to delete user');
            },
        });
    };

    const columns: ColumnDef<App.Data.UserData>[] = [
        {
            accessorKey: 'name',
            header: 'Name',
        },
        {
            accessorKey: 'email',
            header: 'Email',
        },
        {
            accessorKey: 'roles',
            header: () => <div className="text-center">Role</div>,
            cell: ({ row }) => {
                const roles = row.original.roles;
                const formatRoleName = (name: string) =>
                    name
                        .replace(/_/g, ' ')
                        .replace(/^\w/, (c) => c.toUpperCase());
                return (
                    <div className="text-center">
                        {roles.map((r) => formatRoleName(r.name)).join(', ')}
                    </div>
                );
            },
        },
        {
            accessorKey: 'updatedAt',
            header: () => <div className="text-right">Updated At</div>,
            cell: ({ row }) => {
                const dateStr = row.original.updatedAt;
                return (
                    <div className="text-right font-medium">
                        {format(parseISO(dateStr), 'MMM d, yyyy')}
                    </div>
                );
            },
        },
        {
            id: 'actions',
            cell: ({ row }) => {
                const user = row.original;

                return (
                    <DropdownMenu>
                        <DropdownMenuTrigger asChild>
                            <Button variant="ghost" className="h-8 w-8 p-0">
                                <span className="sr-only">Open menu</span>
                                <MoreHorizontal className="h-4 w-4" />
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end">
                            <DropdownMenuLabel>Actions</DropdownMenuLabel>
                            <DropdownMenuItem
                                onClick={() => {
                                    navigator.clipboard.writeText(
                                        user.id.toString(),
                                    );
                                    setCopiedId(user.id);
                                    setTimeout(() => setCopiedId(null), 2000);
                                    toast.success(
                                        'User ID copied to clipboard',
                                    );
                                }}
                            >
                                {copiedId === user.id ? (
                                    <Check className="mr-2 h-4 w-4" />
                                ) : (
                                    <Clipboard className="mr-2 h-4 w-4" />
                                )}
                                Copy User ID
                            </DropdownMenuItem>
                            <DropdownMenuSeparator />
                            <DropdownMenuItem asChild>
                                <Link href={edit.url(user.id)}>
                                    <Pencil className="mr-2 h-4 w-4" />
                                    Edit User
                                </Link>
                            </DropdownMenuItem>
                            <DropdownMenuItem
                                className="text-destructive"
                                onClick={() => setUserToDelete(user)}
                            >
                                <Trash2 className="mr-2 h-4 w-4" />
                                Delete User
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>
                );
            },
        },
    ];

    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Admin', href: '#' },
                { title: 'Users', href: '#' },
            ]}
        >
            <Head title="Admin Users" />
            <div className="flex flex-col gap-6 p-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold tracking-tight">
                            Users
                        </h1>
                        <p className="text-muted-foreground">
                            Manage your users and their permissions here.
                        </p>
                    </div>
                    <Button asChild>
                        <Link href={create.url()}>Add User</Link>
                    </Button>
                </div>
                <AppDataTable columns={columns} data={users} />
            </div>

            <AlertDialog
                open={!!userToDelete}
                onOpenChange={(open) => !open && setUserToDelete(null)}
            >
                <AlertDialogContent>
                    <AlertDialogHeader>
                        <AlertDialogTitle>
                            Are you sure you want to delete this user?
                        </AlertDialogTitle>
                        <AlertDialogDescription>
                            This action will soft delete the user account. They
                            will no longer be able to log in.
                        </AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                        <AlertDialogCancel disabled={processing}>
                            Cancel
                        </AlertDialogCancel>
                        <AlertDialogAction
                            onClick={handleDelete}
                            disabled={processing}
                            className="bg-destructive text-white hover:bg-destructive/90"
                        >
                            Delete
                        </AlertDialogAction>
                    </AlertDialogFooter>
                </AlertDialogContent>
            </AlertDialog>
        </AppLayout>
    );
}
