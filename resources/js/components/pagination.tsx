import { Button } from '@/components/ui/button';
import { Link } from '@inertiajs/react';
import { ChevronLeftIcon, ChevronRightIcon, MoreHorizontalIcon } from 'lucide-react';

interface PaginationProps {
    links: {
        url: string | null;
        label: string;
        active: boolean;
    }[];
}

export default function Pagination({ links }: PaginationProps) {
    // Don't render pagination if there's only 1 page
    if (links.length <= 3) {
        return null;
    }

    return (
        <div className="flex items-center justify-center space-x-1">
            {links.map((link, i) => {
                // Skip the "Next" and "Previous" labels at the beginning and end
                if (i === 0 || i === links.length - 1) {
                    return null;
                }

                // For the previous button
                if (i === 1) {
                    return (
                        <Button key="previous" variant="outline" size="icon" disabled={!links[0].url} asChild={links[0].url ? true : undefined}>
                            {links[0].url ? (
                                <Link href={links[0].url}>
                                    <ChevronLeftIcon className="h-4 w-4" />
                                    <span className="sr-only">Previous</span>
                                </Link>
                            ) : (
                                <span>
                                    <ChevronLeftIcon className="h-4 w-4" />
                                    <span className="sr-only">Previous</span>
                                </span>
                            )}
                        </Button>
                    );
                }

                // For the next button
                if (i === links.length - 2) {
                    return (
                        <Button
                            key="next"
                            variant="outline"
                            size="icon"
                            disabled={!links[links.length - 1].url}
                            asChild={links[links.length - 1].url ? true : undefined}
                        >
                            {links[links.length - 1].url ? (
                                <Link href={links[links.length - 1].url}>
                                    <ChevronRightIcon className="h-4 w-4" />
                                    <span className="sr-only">Next</span>
                                </Link>
                            ) : (
                                <span>
                                    <ChevronRightIcon className="h-4 w-4" />
                                    <span className="sr-only">Next</span>
                                </span>
                            )}
                        </Button>
                    );
                }

                // For ellipsis
                if (link.label === '...') {
                    return (
                        <Button key={i} variant="outline" size="icon" disabled>
                            <MoreHorizontalIcon className="h-4 w-4" />
                            <span className="sr-only">More pages</span>
                        </Button>
                    );
                }

                // For regular page numbers
                return (
                    <Button key={i} variant={link.active ? 'default' : 'outline'} size="icon" asChild={!link.active && link.url ? true : undefined}>
                        {!link.active && link.url ? (
                            <Link href={link.url}>
                                <span>{link.label}</span>
                            </Link>
                        ) : (
                            <span>{link.label}</span>
                        )}
                    </Button>
                );
            })}
        </div>
    );
}
