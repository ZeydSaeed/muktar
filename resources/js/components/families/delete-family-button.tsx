import { Form } from '@inertiajs/react';
import { Trash2 } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import {
    Tooltip,
    TooltipContent,
    TooltipTrigger,
} from '@/components/ui/tooltip';

type Props = {
    familyId: number;
    familyName: string;
};

export function DeleteFamilyButton({ familyId, familyName }: Props) {
    return (
        <Form
            action={`/families/${familyId}`}
            method="delete"
            options={{ preserveScroll: true }}
            onSubmit={(event) => {
                if (!window.confirm(`هل تريد حذف عائلة ${familyName}؟`)) {
                    event.preventDefault();
                }
            }}
        >
            {({ processing }) => (
                <Tooltip>
                    <TooltipTrigger asChild>
                        <Button
                            type="submit"
                            size="icon"
                            variant="ghost"
                            disabled={processing}
                            aria-label="حذف عائلة"
                            className="text-destructive hover:bg-destructive/10 size-8"
                        >
                            {processing ? (
                                <Spinner />
                            ) : (
                                <Trash2 className="size-4" />
                            )}
                        </Button>
                    </TooltipTrigger>
                    <TooltipContent>حذف</TooltipContent>
                </Tooltip>
            )}
        </Form>
    );
}
