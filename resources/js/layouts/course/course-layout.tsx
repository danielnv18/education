import Heading from '@/components/heading';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { cn } from '@/lib/utils';
import { type Course, type NavItem } from '@/types';
import { Link } from '@inertiajs/react';
import { BookCheck, PencilLine, Users as UsersIcon } from 'lucide-react';
import { type PropsWithChildren } from 'react';

interface CourseLayoutProps extends PropsWithChildren {
    course: Course;
}

export default function CourseLayout({ children, course }: CourseLayoutProps) {
    // When server-side rendering, we only render the layout on the client...
    if (typeof window === 'undefined') {
        return null;
    }

    const currentPath = window.location.href;

    const sidebarNavItems: NavItem[] = [
        {
            title: 'Edit',
            href: route('courses.edit', { course: course.id }),
            icon: PencilLine,
        },
        {
            title: 'Students',
            href: route('courses.students.index', { course: course.id }),
            icon: UsersIcon,
        },
        {
            title: 'Content',
            href: route('courses.content.index', { course: course.id }),
            icon: BookCheck,
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
                                    {item.icon && <item.icon className="mr-2 h-4 w-4" />}
                                    {item.title}
                                </Link>
                            </Button>
                        ))}
                    </nav>
                </aside>

                <Separator className="my-6 md:hidden" />

                <div className="flex-1">
                    <section className="space-y-12">{children}</section>
                </div>
            </div>
        </div>
    );
}
