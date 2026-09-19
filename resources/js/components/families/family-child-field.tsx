import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

type Props = {
    label: string;
    name: string;
    value: string;
    onChange: (value: string) => void;
    type?: string;
    required?: boolean;
    error?: string;
};

export function FamilyChildField({
    label,
    name,
    value,
    onChange,
    type = 'text',
    required = false,
    error,
}: Props) {
    return (
        <div className="grid gap-1.5">
            <Label htmlFor={name}>{label}</Label>
            <Input
                id={name}
                name={name}
                type={type}
                required={required}
                value={value}
                dir={type === 'date' ? 'ltr' : undefined}
                onChange={(event) => onChange(event.target.value)}
            />
            {error ? <p className="text-destructive text-xs">{error}</p> : null}
        </div>
    );
}
