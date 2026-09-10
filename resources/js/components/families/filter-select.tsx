import { cn } from '@/lib/utils';

type Option = {
    value: string;
    label: string;
};

type Props = {
    id: string;
    label?: string;
    value: string;
    options: Option[];
    allLabel?: string;
    onChange: (value: string) => void;
};

export function FilterSelect({
    id,
    label,
    value,
    options,
    allLabel = 'الكل',
    onChange,
}: Props) {
    return (
        <div className="grid min-w-0 gap-0.5">
            {label ? (
                <label htmlFor={id} className="text-[10px] leading-tight font-semibold">
                    {label}
                </label>
            ) : (
                <span className="sr-only">{allLabel}</span>
            )}
            <select
                id={id}
                value={value}
                onChange={(event) => onChange(event.target.value)}
                className={cn(
                    'border-primary bg-card text-foreground h-7 w-full min-w-0 rounded border px-1 text-[11px]',
                    'focus-visible:ring-ring outline-none focus-visible:ring-1',
                )}
            >
                <option value="">{allLabel}</option>
                {options.map((option) => (
                    <option key={option.value} value={option.value}>
                        {option.label}
                    </option>
                ))}
            </select>
        </div>
    );
}
