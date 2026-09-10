import { Link } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';
import type { PaginationLink } from '@/types';

type Props = {
    links: PaginationLink[];
};

function labelFor(link: PaginationLink): string {
    if (link.label.includes('Previous')) {
        return 'السابق';
    }

    if (link.label.includes('Next')) {
        return 'التالي';
    }

    return link.label.replace('&laquo;', '').replace('&raquo;', '').trim();
}

export function TablePagination({ links }: Props) {
    if (links.length <= 3) {
        return null;
    }

    return (
        <nav
            aria-label="ترقيم الصفحات"
            className="flex flex-wrap items-center justify-center gap-1"
        >
            {links.map((link, index) => {
                const label = labelFor(link);
                const isDisabled = link.url === null;

                if (isDisabled) {
                    return (
                        <Button
                            key={`${label}-${index}`}
                            variant="outline"
                            size="sm"
                            disabled
                            className="min-w-9"
                        >
                            {label}
                        </Button>
                    );
                }

                return (
                    <Button
                        key={`${label}-${index}`}
                        variant={link.active ? 'default' : 'outline'}
                        size="sm"
                        className={cn(
                            'min-w-9',
                            link.active && 'pointer-events-none',
                        )}
                        asChild
                    >
                        <Link href={link.url} preserveScroll>
                            {label}
                        </Link>
                    </Button>
                );
            })}
        </nav>
    );
}
