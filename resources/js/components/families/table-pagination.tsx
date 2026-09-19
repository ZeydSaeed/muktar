import { Link } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';
import type { PaginationLink } from '@/types';

type Props = {
    links: PaginationLink[];
};

function labelFor(link: PaginationLink): string {
    const raw = link.label
        .replace('&laquo;', '')
        .replace('&raquo;', '')
        .trim();

    if (
        raw.includes('Previous') ||
        raw.includes('pagination.previous') ||
        raw === 'السابق'
    ) {
        return 'السابق';
    }

    if (
        raw.includes('Next') ||
        raw.includes('pagination.next') ||
        raw === 'التالي'
    ) {
        return 'التالي';
    }

    return raw;
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
                            className="min-w-8 h-6 px-2 text-[13px]"
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
                            'min-w-8 h-6 px-2 text-[13px]',
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
