import {
  Pagination,
  PaginationContent,
  PaginationItem,
  PaginationLink,
  PaginationPrevious,
  PaginationNext,
  PaginationEllipsis,
} from '@/components/ui/pagination';

interface AppPaginationProps {
  currentPage: number;
  totalPages: number;
  onPageChange?: (page: number) => void;
}

function getPaginationRange(current: number, total: number) {
  const delta = 2;
  const range = [];
  for (let i = Math.max(2, current - delta); i <= Math.min(total - 1, current + delta); i++) {
    range.push(i);
  }
  if (current - delta > 2) {
    range.unshift('...');
  }
  if (current + delta < total - 1) {
    range.push('...');
  }
  range.unshift(1);
  if (total > 1) range.push(total);
  return range;
}

export default function AppPagination({ currentPage, totalPages, onPageChange }: AppPaginationProps) {
  if (totalPages <= 1) return null;
  const range = getPaginationRange(currentPage, totalPages);
  return (
    <Pagination>
      <PaginationContent>
        <PaginationItem>
          <PaginationPrevious
            aria-disabled={currentPage === 1}
            tabIndex={currentPage === 1 ? -1 : 0}
            onClick={currentPage > 1 && onPageChange ? () => onPageChange(currentPage - 1) : undefined}
          />
        </PaginationItem>
        {range.map((page, idx) =>
          page === '...'
            ? (
                <PaginationItem key={`ellipsis-${idx}`}>
                  <PaginationEllipsis />
                </PaginationItem>
              )
            : (
                <PaginationItem key={`page-${page}`}>
                  <PaginationLink
                    isActive={page === currentPage}
                    href={undefined}
                    aria-current={page === currentPage ? 'page' : undefined}
                    onClick={onPageChange && page !== currentPage ? () => onPageChange(Number(page)) : undefined}
                    style={{ cursor: page !== currentPage ? 'pointer' : 'default' }}
                  >
                    {page}
                  </PaginationLink>
                </PaginationItem>
              )
        )}
        <PaginationItem>
          <PaginationNext
            aria-disabled={currentPage === totalPages}
            tabIndex={currentPage === totalPages ? -1 : 0}
            onClick={currentPage < totalPages && onPageChange ? () => onPageChange(currentPage + 1) : undefined}
          />
        </PaginationItem>
      </PaginationContent>
    </Pagination>
  );
}
