import {
    type ColumnDef,
    type ColumnFiltersState,
    type SortingState,
    type VisibilityState,
    flexRender,
    getCoreRowModel,
    getFilteredRowModel,
    getPaginationRowModel,
    getSortedRowModel,
    useReactTable,
} from '@tanstack/react-table';
import * as React from 'react';

import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';

import {
    Pagination,
    PaginationContent,
    PaginationEllipsis,
    PaginationFirst,
    PaginationItem,
    PaginationLast,
    PaginationLink,
    PaginationNext,
    PaginationPrevious,
} from '@/components/ui/pagination';

interface AppDataTableProps<TData, TValue> {
    columns: ColumnDef<TData, TValue>[];
    data: TData[];
}

export function AppDataTable<TData, TValue>({
    columns,
    data,
}: AppDataTableProps<TData, TValue>) {
    const [sorting, setSorting] = React.useState<SortingState>([]);
    const [columnFilters, setColumnFilters] =
        React.useState<ColumnFiltersState>([]);
    const [columnVisibility, setColumnVisibility] =
        React.useState<VisibilityState>({});
    const [rowSelection, setRowSelection] = React.useState({});

    const table = useReactTable({
        data,
        columns,
        getCoreRowModel: getCoreRowModel(),
        getPaginationRowModel: getPaginationRowModel(),
        onSortingChange: setSorting,
        getSortedRowModel: getSortedRowModel(),
        onColumnFiltersChange: setColumnFilters,
        getFilteredRowModel: getFilteredRowModel(),
        onColumnVisibilityChange: setColumnVisibility,
        onRowSelectionChange: setRowSelection,
        state: {
            sorting,
            columnFilters,
            columnVisibility,
            rowSelection,
        },
    });

    return (
        <div className="space-y-4">
            <div className="rounded-md border">
                <Table>
                    <TableHeader>
                        {table.getHeaderGroups().map((headerGroup) => (
                            <TableRow key={headerGroup.id}>
                                {headerGroup.headers.map((header) => {
                                    return (
                                        <TableHead key={header.id}>
                                            {header.isPlaceholder
                                                ? null
                                                : flexRender(
                                                      header.column.columnDef
                                                          .header,
                                                      header.getContext(),
                                                  )}
                                        </TableHead>
                                    );
                                })}
                            </TableRow>
                        ))}
                    </TableHeader>
                    <TableBody>
                        {table.getRowModel().rows?.length ? (
                            table.getRowModel().rows.map((row) => (
                                <TableRow
                                    key={row.id}
                                    data-state={
                                        row.getIsSelected() && 'selected'
                                    }
                                >
                                    {row.getVisibleCells().map((cell) => (
                                        <TableCell key={cell.id}>
                                            {flexRender(
                                                cell.column.columnDef.cell,
                                                cell.getContext(),
                                            )}
                                        </TableCell>
                                    ))}
                                </TableRow>
                            ))
                        ) : (
                            <TableRow>
                                <TableCell
                                    colSpan={columns.length}
                                    className="h-24 text-center"
                                >
                                    No results.
                                </TableCell>
                            </TableRow>
                        )}
                    </TableBody>
                </Table>
            </div>

            <div className="flex items-center justify-end px-2">
                <div className="flex items-center space-x-6 lg:space-x-8">
                    <Pagination className="w-auto">
                        <PaginationContent>
                            <PaginationItem>
                                <PaginationFirst
                                    href="#"
                                    onClick={(e) => {
                                        e.preventDefault();
                                        table.setPageIndex(0);
                                    }}
                                    aria-disabled={!table.getCanPreviousPage()}
                                    className={
                                        !table.getCanPreviousPage()
                                            ? 'pointer-events-none opacity-50'
                                            : ''
                                    }
                                />
                            </PaginationItem>
                            <PaginationItem>
                                <PaginationPrevious
                                    href="#"
                                    onClick={(e) => {
                                        e.preventDefault();
                                        table.previousPage();
                                    }}
                                    aria-disabled={!table.getCanPreviousPage()}
                                    className={
                                        !table.getCanPreviousPage()
                                            ? 'pointer-events-none opacity-50'
                                            : ''
                                    }
                                />
                            </PaginationItem>
                            {(() => {
                                const pageIndex =
                                    table.getState().pagination.pageIndex;
                                const pageCount = table.getPageCount();
                                const currentPage = pageIndex + 1;
                                const pages: (number | 'ellipsis')[] = [];

                                if (pageCount <= 5) {
                                    for (let i = 1; i <= pageCount; i++) {
                                        pages.push(i);
                                    }
                                } else {
                                    if (currentPage <= 3) {
                                        pages.push(
                                            1,
                                            2,
                                            3,
                                            4,
                                            'ellipsis',
                                            pageCount,
                                        );
                                    } else if (currentPage >= pageCount - 2) {
                                        pages.push(
                                            1,
                                            'ellipsis',
                                            pageCount - 3,
                                            pageCount - 2,
                                            pageCount - 1,
                                            pageCount,
                                        );
                                    } else {
                                        pages.push(
                                            1,
                                            'ellipsis',
                                            currentPage - 1,
                                            currentPage,
                                            currentPage + 1,
                                            'ellipsis',
                                            pageCount,
                                        );
                                    }
                                }

                                return pages.map((page, index) => {
                                    if (page === 'ellipsis') {
                                        return (
                                            <PaginationItem
                                                key={`ellipsis-${index}`}
                                                className="hidden sm:block"
                                            >
                                                <PaginationEllipsis />
                                            </PaginationItem>
                                        );
                                    }

                                    return (
                                        <PaginationItem
                                            key={page}
                                            className="hidden sm:block"
                                        >
                                            <PaginationLink
                                                href="#"
                                                onClick={(e) => {
                                                    e.preventDefault();
                                                    table.setPageIndex(
                                                        Number(page) - 1,
                                                    );
                                                }}
                                                isActive={currentPage === page}
                                            >
                                                {page}
                                            </PaginationLink>
                                        </PaginationItem>
                                    );
                                });
                            })()}
                            <PaginationItem>
                                <PaginationNext
                                    href="#"
                                    onClick={(e) => {
                                        e.preventDefault();
                                        table.nextPage();
                                    }}
                                    aria-disabled={!table.getCanNextPage()}
                                    className={
                                        !table.getCanNextPage()
                                            ? 'pointer-events-none opacity-50'
                                            : ''
                                    }
                                />
                            </PaginationItem>
                            <PaginationItem>
                                <PaginationLast
                                    href="#"
                                    onClick={(e) => {
                                        e.preventDefault();
                                        table.setPageIndex(
                                            table.getPageCount() - 1,
                                        );
                                    }}
                                    aria-disabled={!table.getCanNextPage()}
                                    className={
                                        !table.getCanNextPage()
                                            ? 'pointer-events-none opacity-50'
                                            : ''
                                    }
                                />
                            </PaginationItem>
                        </PaginationContent>
                    </Pagination>
                </div>
            </div>
        </div>
    );
}
