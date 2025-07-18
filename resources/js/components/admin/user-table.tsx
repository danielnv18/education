import AppPagination from '@/components/app-pagination';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Link } from '@inertiajs/react';
import { createColumnHelper, flexRender, getCoreRowModel, useReactTable } from '@tanstack/react-table';
import { PencilIcon } from 'lucide-react';
import * as React from 'react';
import DeleteUserModal from './delete-user-modal';

interface UserTableProps {
    users: App.Data.UserData[];
}

const columnHelper = createColumnHelper<App.Data.UserData>();

export default function UserTable({ users }: UserTableProps) {
    const [page, setPage] = React.useState(0);
    const pageSize = 10;
    const pageCount = Math.ceil(users.length / pageSize);
    const pagedUsers = React.useMemo(() => users.slice(page * pageSize, (page + 1) * pageSize), [users, page]);

    const columns = React.useMemo(
        () => [
            columnHelper.accessor('name', {
                header: 'Name',
                cell: (info) => (
                    <Link href={route('admin.users.show', { user: info.row.original.id })} className="hover:underline">
                        {info.getValue()}
                    </Link>
                ),
            }),
            columnHelper.accessor('email', {
                header: 'Email',
            }),
            columnHelper.accessor('roles', {
                header: 'Roles',
                cell: (info) => (
                    <div className="flex flex-wrap gap-1">
                        {info.getValue().map((role: string | { name: string }) => (
                            <Badge key={typeof role === 'object' ? role.name : role} variant="outline">
                                {typeof role === 'object' ? role.name : role}
                            </Badge>
                        ))}
                    </div>
                ),
            }),
            columnHelper.display({
                id: 'actions',
                header: 'Actions',
                cell: (info) => (
                    <div className="flex space-x-2">
                        <Button variant="ghost" size="icon" asChild>
                            <Link href={route('admin.users.edit', { user: info.row.original.id })}>
                                <PencilIcon className="h-4 w-4" />
                                <span className="sr-only">Edit</span>
                            </Link>
                        </Button>
                        <DeleteUserModal user={info.row.original} />
                    </div>
                ),
            }),
        ],
        [],
    );

    const table = useReactTable({
        data: pagedUsers,
        columns,
        getCoreRowModel: getCoreRowModel(),
        manualPagination: true,
        pageCount,
        state: { pagination: { pageIndex: page, pageSize } },
    });

    return (
        <div className="space-y-4">
            <div className="rounded-md border">
                <Table>
                    <TableHeader>
                        {table.getHeaderGroups().map((headerGroup) => (
                            <TableRow key={headerGroup.id}>
                                {headerGroup.headers.map((header) => (
                                    <TableHead key={header.id} className={header.column.id === 'actions' ? 'w-[100px]' : ''}>
                                        {header.isPlaceholder ? null : flexRender(header.column.columnDef.header, header.getContext())}
                                    </TableHead>
                                ))}
                            </TableRow>
                        ))}
                    </TableHeader>
                    <TableBody>
                        {table.getRowModel().rows.map((row) => (
                            <TableRow key={row.id}>
                                {row.getVisibleCells().map((cell) => (
                                    <TableCell key={cell.id}>{flexRender(cell.column.columnDef.cell, cell.getContext())}</TableCell>
                                ))}
                            </TableRow>
                        ))}
                    </TableBody>
                </Table>
            </div>
            <AppPagination currentPage={page + 1} totalPages={pageCount} onPageChange={(p) => setPage(p - 1)} />
        </div>
    );
}
