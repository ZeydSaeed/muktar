import type { ReactNode } from 'react';

type Props = {
    title: string;
    description?: string;
    children: ReactNode;
};

export function DataToolPanel({ title, description, children }: Props) {
    return (
        <div className="flex h-full flex-1 flex-col gap-6 p-4">
            <div className="space-y-2">
                <h1 className="text-xl font-semibold">{title}</h1>
                {description ? (
                    <p className="text-muted-foreground max-w-2xl text-sm leading-relaxed">
                        {description}
                    </p>
                ) : null}
            </div>
            <div className="max-w-xl space-y-4 rounded-xl border-2 border-primary bg-card p-4">
                {children}
            </div>
        </div>
    );
}
