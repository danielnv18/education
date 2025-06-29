import Heading from '@/components/heading';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { cn } from '@/lib/utils';
import { type Course, type NavItem } from '@/types';
import { Link } from '@inertiajs/react';
import { Users as UsersIcon } from 'lucide-react';
import { type PropsWithChildren } from 'react';

interface CourseLayoutProps extends PropsWithChildren {
    course: Course;
}

export default function CourseLayout({ children, course }: CourseLayoutProps) {
    // When server-side rendering, we only render the layout on the client...
    if (typeof window === 'undefined') {
        return null;
    }

    const currentPath = window.location.pathname;

    const sidebarNavItems: NavItem[] = [
        {
            title: 'Edit',
            href: route('courses.edit', { course: course.id }),
            icon: null,
        },
        {
            title: 'Students',
            href: route('courses.students', { course: course.id }),
            icon: UsersIcon,
        },
    ];

    return (
        <div className="px-4 py-6">
            <Heading title={course.title} description="Manage your course settings and students" />

            <div className="flex flex-col space-y-8 lg:flex-row lg:space-y-0 lg:space-x-12">
                <aside className="w-full max-w-xl lg:w-48">
                    <nav className="flex flex-col space-y-1 space-x-0">
                        {sidebarNavItems.map((item, index) => (
                            <Button
                                key={`${item.href}-${index}`}
                                size="sm"
                                variant="ghost"
                                asChild
                                className={cn('w-full justify-start', {
                                    'bg-muted': currentPath === item.href,
                                })}
                            >
                                <Link href={item.href} prefetch>
                                    {item.title}
                                </Link>
                            </Button>
                        ))}
                    </nav>
                </aside>

                <Separator className="my-6 md:hidden" />

                <div className="flex-1 md:max-w-2xl">
                    <section className="max-w-xl space-y-12">{children}</section>
                </div>
            </div>
        </div>
    );
}
