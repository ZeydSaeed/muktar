import { escapeRegExp, searchTokens } from '@/lib/family-search';

type Props = {
    name: string;
    query: string;
};

export function HighlightedName({ name, query }: Props) {
    const tokens = searchTokens(query);

    if (tokens.length === 0) {
        return name;
    }

    const pattern = tokens.map(escapeRegExp).join('|');
    const parts = name.split(new RegExp(`(${pattern})`, 'gi'));

    return (
        <>
            {parts.map((part, index) => {
                const isMatch = tokens.some(
                    (token) => token.toLocaleLowerCase() === part.toLocaleLowerCase(),
                );

                if (!isMatch) {
                    return <span key={`${part}-${index}`}>{part}</span>;
                }

                return (
                    <mark
                        key={`${part}-${index}`}
                        className="bg-accent text-accent-foreground rounded-sm px-0.5"
                    >
                        {part}
                    </mark>
                );
            })}
        </>
    );
}
