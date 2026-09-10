import { Form, Head } from '@inertiajs/react';
import { FamilyFormFields } from '@/components/families/family-form-fields';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';

type FamilyFormValues = Record<string, string | number | null> & {
    id: number;
};

type Props = {
    family: FamilyFormValues;
};

export default function EditFamily({ family }: Props) {
    return (
        <>
            <Head title="تعديل عائلة" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-y-auto p-4">
                <h1 className="text-xl font-semibold">تعديل عائلة</h1>

                <Form
                    action={`/families/${family.id}`}
                    method="put"
                    className="max-w-5xl space-y-6"
                >
                    {({ processing, errors }) => (
                        <>
                            <FamilyFormFields errors={errors} values={family} />
                            <Button type="submit" disabled={processing}>
                                {processing && <Spinner />}
                                حفظ التعديل
                            </Button>
                        </>
                    )}
                </Form>
            </div>
        </>
    );
}

EditFamily.layout = {
    breadcrumbs: [],
};
