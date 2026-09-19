type Props = {
    heading: string;
    description: string;
    children: React.ReactNode;
};

export function FamilyFormDocument({ heading, description, children }: Props) {
    return (
        <div className="flex-1 overflow-y-auto p-4">
            <article
                dir="rtl"
                className="border-primary bg-card mx-auto w-full max-w-6xl space-y-6 rounded-xl border-2 p-5"
            >
                <header className="space-y-2 text-center">
                    <h2 className="font-bold">{heading}</h2>
                    <p className="text-muted-foreground">{description}</p>
                    <span className="bg-accent mx-auto block h-0.5 w-24" />
                </header>
                {children}
            </article>
        </div>
    );
}
