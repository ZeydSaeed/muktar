import { cn } from '@/lib/utils';

type Props = {
    label: string;
    value?: string | number | null;
    className?: string;
};

export function RecordLine({ label, value, className }: Props) {
    return (
        <div className={cn('flex items-baseline gap-2 text-sm', className)}>
            <span className="shrink-0 font-semibold">{label} :</span>
            <span className="border-primary min-h-6 min-w-12 flex-1 border-b border-dotted px-1">
                {value === null || value === undefined || value === ''
                    ? '\u00a0'
                    : String(value)}
            </span>
        </div>
    );
}

export function RecordInline({ label, value, className }: Props) {
    return (
        <div className={cn('flex min-w-0 flex-1 items-baseline gap-1 text-sm', className)}>
            <span className="shrink-0">{label} :</span>
            <span className="border-primary min-h-6 min-w-8 flex-1 border-b border-dotted px-1">
                {value === null || value === undefined || value === ''
                    ? '\u00a0'
                    : String(value)}
            </span>
        </div>
    );
}
