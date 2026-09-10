import { Search, X } from 'lucide-react';
import { useFamilySearch } from '@/hooks/use-family-search';
import { Button } from '@/components/ui/button';

type Props = {
    search: string;
};

export function FamiliesSearchBar({ search }: Props) {
    const { value, setValue, clearSearch } = useFamilySearch(search);

    return (
        <div className="border-primary flex w-full border-b-2">
            <label className="flex min-w-0 flex-1 items-center gap-2 px-3">
                <Search className="text-primary size-4 shrink-0" />
                <input
                    type="text"
                    value={value}
                    onChange={(event) => setValue(event.target.value)}
                    placeholder="ابحث عن اسم رب الاسرة"
                    aria-label="ابحث عن اسم رب الاسرة"
                    className="text-foreground placeholder:text-muted-foreground h-11 min-w-0 flex-1 bg-transparent text-sm outline-none"
                />
            </label>
            <Button
                type="button"
                variant="default"
                className="border-primary h-11 rounded-none border-s-2 px-4"
                onClick={clearSearch}
            >
                <X className="size-4" />
                الغاء البحث
            </Button>
        </div>
    );
}
