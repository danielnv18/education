import { type ColumnDef } from '@tanstack/react-table';
import { Check, Clipboard, MoreHorizontal } from 'lucide-react';

import { AppDataTable } from '@/components/app-datatable';
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
import { Head } from '@inertiajs/react';
import { useState } from 'react';
import { toast } from 'sonner';

// Mock data generator
const generateUsers = (count: number): App.Data.UserData[] => {
    return Array.from({ length: count }).map((_, i) => ({
        id: i + 1,
        name: `User ${i + 1}`,
        email: `user${i + 1}@example.com`,
        emailVerifiedAt: i % 3 === 0 ? null : new Date().toISOString(),
        createdAt: new Date(
            Date.now() - Math.random() * 10000000000,
        ).toISOString(),
        updatedAt: new Date().toISOString(),
        roles: [
            {
                name:
                    i % 10 === 0
                        ? 'Admin'
                        : i % 5 === 0
                            ? 'Editor'
                            : 'User',
            },
        ],
        avatar: null,
    }));
};

const users = generateUsers(200);

export default function AdminUsers() {
    const [copiedId, setCopiedId] = useState<number | null>(null);

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
            header: 'Role',
            cell: ({ row }) => {
                const roles = row.original.roles;
                return (
                    <div className="capitalize">
                        {roles.map((r) => r.name).join(', ')}
                    </div>
                );
            },
        },
        {
            accessorKey: 'createdAt',
            header: 'Created At',
            cell: ({ row }) => {
                const date = new Date(row.getValue('createdAt'));
                return (
                    <div className="text-right font-medium">
                        {date.toLocaleDateString()}
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
                            <DropdownMenuItem>View customer</DropdownMenuItem>
                            <DropdownMenuItem>
                                View payment details
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
                { title: 'Admin', href: '/admin/users' },
                { title: 'Users', href: '/admin/users' },
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
                    <Button>Add User</Button>
                </div>
                <AppDataTable columns={columns} data={users} />
            </div>
        </AppLayout>
    );
}
