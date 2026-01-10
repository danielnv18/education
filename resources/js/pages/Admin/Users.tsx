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

type User = {
    id: string;
    name: string;
    email: string;
    role: 'admin' | 'user' | 'editor';
    status: 'active' | 'inactive' | 'banned';
    lastLogin: string;
};

// Mock data generator
const generateUsers = (count: number): User[] => {
    return Array.from({ length: count }).map((_, i) => ({
        id: `user-${i + 1}`,
        name: `User ${i + 1}`,
        email: `user${i + 1}@example.com`,
        role: i % 10 === 0 ? 'admin' : i % 5 === 0 ? 'editor' : 'user',
        status: i % 3 === 0 ? 'inactive' : i % 7 === 0 ? 'banned' : 'active',
        lastLogin: new Date(
            Date.now() - Math.random() * 10000000000,
        ).toISOString(),
    }));
};

const users = generateUsers(200);

export default function AdminUsers() {
    const [copiedId, setCopiedId] = useState<string | null>(null);

    const columns: ColumnDef<User>[] = [
        {
            accessorKey: 'name',
            header: 'Name',
        },
        {
            accessorKey: 'email',
            header: 'Email',
        },
        {
            accessorKey: 'role',
            header: 'Role',
            cell: ({ row }) => {
                const role = row.getValue('role') as string;
                return <div className="capitalize">{role}</div>;
            },
        },
        {
            accessorKey: 'status',
            header: 'Status',
            cell: ({ row }) => {
                const status = row.getValue('status') as string;
                return (
                    <div
                        className={`capitalize ${status === 'active' ? 'text-green-600' : status === 'banned' ? 'text-red-600' : 'text-gray-500'}`}
                    >
                        {status}
                    </div>
                );
            },
        },
        {
            accessorKey: 'lastLogin',
            header: 'Last Login',
            cell: ({ row }) => {
                const date = new Date(row.getValue('lastLogin'));
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
                                    navigator.clipboard.writeText(user.id);
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
