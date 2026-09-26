import type { ReactNode } from 'react';

type Props = {
    title: string;
    description?: string;
    preamble?: ReactNode;
    children: ReactNode;
};

export function DataToolPanel({
    title,
    description,
    preamble,
    children,
}: Props) {
    return (
        <div className="flex h-full min-h-0 flex-1 flex-col gap-4 overflow-hidden p-4">
            <div className="shrink-0 space-y-2">
                <h1 className="text-xl font-semibold">{title}</h1>
                {description ? (
                    <p className="text-muted-foreground max-w-2xl text-sm leading-relaxed">
                        {description}
                    </p>
                ) : null}
            </div>
            {preamble ? (
                <div className="flex min-h-0 flex-1 flex-col overflow-hidden">
                    {preamble}
                </div>
            ) : null}
            <div className="max-w-xl shrink-0 space-y-4 rounded-xl border-2 border-primary bg-card p-4">
                {children}
            </div>
        </div>
    );
}
