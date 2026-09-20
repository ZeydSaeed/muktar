import { Form, usePage } from '@inertiajs/react';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

export function NeighborhoodForm() {
    const { neighborhood } = usePage().props;

    return (
        <Form
            action="/settings/neighborhood"
            method="patch"
            options={{ preserveScroll: true }}
            className="flex flex-col gap-2 sm:flex-row sm:items-end"
        >
            {({ processing, errors }) => (
                <>
                    <div className="min-w-0 flex-1">
                        <Label htmlFor="neighborhood">اسم الحي</Label>
                        <Input
                            id="neighborhood"
                            name="neighborhood"
                            required
                            maxLength={255}
                            key={neighborhood}
                            defaultValue={neighborhood}
                            autoComplete="off"
                            className="mt-1"
                        />
                        <InputError message={errors.neighborhood} />
                    </div>
                    <Button disabled={processing} className="shrink-0">
                        حفظ
                    </Button>
                </>
            )}
        </Form>
    );
}
