import { FamilyFormField } from '@/components/families/family-form-field';
import { FamilyFormSection } from '@/components/families/family-form-section';
import InputError from '@/components/input-error';
import {
    familyFormSections,
    type FamilyFormSectionDef,
} from '@/lib/family-form-schema';

type FormValues = Record<string, string | number | null>;

type Props = {
    errors: Record<string, string>;
    values?: FormValues;
};

function fieldValue(values: FormValues | undefined, name: string): string {
    const value = values?.[name];

    if (value === null || value === undefined) {
        return '';
    }

    return String(value);
}

function SectionFields({
    section,
    index,
    errors,
    values,
}: {
    section: FamilyFormSectionDef;
    index: number;
    errors: Record<string, string>;
    values?: FormValues;
}) {
    return (
        <FamilyFormSection index={index} title={section.title}>
            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                {section.fields.map((field) => (
                    <FamilyFormField
                        key={field.name}
                        field={field}
                        error={errors[field.name]}
                        defaultValue={fieldValue(values, field.name)}
                    />
                ))}
            </div>
        </FamilyFormSection>
    );
}

function NotesSection({ error, value, index }: {
    error?: string;
    value: string;
    index: number;
}) {
    return (
        <FamilyFormSection index={index} title="الملاحظات">
            <div className="grid gap-1.5">
                <textarea
                    id="notes"
                    name="notes"
                    rows={4}
                    aria-label="الملاحظات"
                    defaultValue={value}
                    className="border-input bg-card focus-visible:border-ring focus-visible:ring-ring/50 min-h-24 w-full rounded-md border px-3 py-2 outline-none focus-visible:ring-[3px]"
                />
                <InputError message={error} />
            </div>
        </FamilyFormSection>
    );
}

export function FamilyFormFields({ errors, values }: Props) {
    return (
        <div className="space-y-6">
            {familyFormSections.map((section, index) => (
                <SectionFields
                    key={section.title}
                    section={section}
                    index={index + 1}
                    errors={errors}
                    values={values}
                />
            ))}
            <NotesSection
                index={familyFormSections.length + 1}
                error={errors.notes}
                value={fieldValue(values, 'notes')}
            />
        </div>
    );
}
