import type { PropsWithChildren } from 'react';

type Props = {
    title: string;
    children: PropsWithChildren['children'];
    inline?: boolean;
};

export function SettingsSection({ title, children, inline = false }: Props) {
    if (inline) {
        return (
            <section className="border-border bg-card flex items-center justify-between gap-3 rounded-lg border px-3 py-2">
                <h2 className="shrink-0 font-semibold">{title}</h2>
                {children}
            </section>
        );
    }

    return (
        <section className="border-border bg-card rounded-lg border px-3 py-2">
            <h2 className="mb-1.5 font-semibold">{title}</h2>
            {children}
        </section>
    );
}
