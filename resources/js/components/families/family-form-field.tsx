import InputError from '@/components/input-error';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { FamilyFormFieldDef } from '@/lib/family-form-schema';
import { cn } from '@/lib/utils';

type Props = {
    field: FamilyFormFieldDef;
    error?: string;
    defaultValue?: string;
};

function spanClass(span?: FamilyFormFieldDef['span']): string {
    if (span === 'full') {
        return 'sm:col-span-2 lg:col-span-3';
    }

    if (span === 'wide') {
        return 'lg:col-span-2';
    }

    return '';
}

export function FamilyFormField({ field, error, defaultValue = '' }: Props) {
    const type = field.type ?? 'text';

    return (
        <div className={cn('grid gap-1.5', spanClass(field.span))}>
            <Label htmlFor={field.name}>
                {field.label}
                {field.required ? (
                    <span className="text-destructive"> *</span>
                ) : null}
            </Label>
            <Input
                id={field.name}
                name={field.name}
                type={type}
                required={field.required}
                defaultValue={defaultValue}
                dir={type === 'date' ? 'ltr' : undefined}
                aria-invalid={error ? true : undefined}
                autoComplete="off"
                className="bg-card"
            />
            <InputError message={error} />
        </div>
    );
}
