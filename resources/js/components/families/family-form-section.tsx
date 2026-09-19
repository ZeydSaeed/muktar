type Props = {
    index: number;
    title: string;
    children: React.ReactNode;
};

export function FamilyFormSection({ index, title, children }: Props) {
    return (
        <section className="space-y-3">
            <header className="flex items-center gap-2">
                <span className="bg-primary text-primary-foreground flex size-6 shrink-0 items-center justify-center rounded-full font-semibold">
                    {index}
                </span>
                <h2 className="font-semibold">{title}</h2>
                <span className="bg-accent h-px min-w-8 flex-1" />
            </header>
            <div className="bg-muted/40 rounded-lg border p-4">{children}</div>
        </section>
    );
}
