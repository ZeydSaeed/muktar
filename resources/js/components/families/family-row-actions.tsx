import { Link } from '@inertiajs/react';
import { Pencil, Trash2, UserPlus } from 'lucide-react';
import { DeleteFamilyButton } from '@/components/families/delete-family-button';
import { Button } from '@/components/ui/button';
import {
    Tooltip,
    TooltipContent,
    TooltipTrigger,
} from '@/components/ui/tooltip';

type Props = {
    familyId: number;
    familyName: string;
};

export function FamilyRowActions({ familyId, familyName }: Props) {
    return (
        <div className="flex items-center justify-center gap-1">
            <Tooltip>
                <TooltipTrigger asChild>
                    <Button
                        size="icon"
                        variant="ghost"
                        className="text-secondary hover:bg-secondary/15 size-7"
                        asChild
                    >
                        <Link href="/families/create" aria-label="اضافة عائلة">
                            <UserPlus className="size-4" />
                        </Link>
                    </Button>
                </TooltipTrigger>
                <TooltipContent>اضافة</TooltipContent>
            </Tooltip>

            <Tooltip>
                <TooltipTrigger asChild>
                    <Button
                        size="icon"
                        variant="ghost"
                        className="text-primary hover:bg-primary/10 size-7"
                        asChild
                    >
                        <Link
                            href={`/families/${familyId}/edit`}
                            aria-label="تعديل عائلة"
                        >
                            <Pencil className="size-4" />
                        </Link>
                    </Button>
                </TooltipTrigger>
                <TooltipContent>تعديل</TooltipContent>
            </Tooltip>

            <DeleteFamilyButton familyId={familyId} familyName={familyName} />
        </div>
    );
}
