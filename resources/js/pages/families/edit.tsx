import { Form, Head } from '@inertiajs/react';
import { FamilyFormDocument } from '@/components/families/family-form-document';
import { FamilyFormFields } from '@/components/families/family-form-fields';
import { FamilyFormToolbar } from '@/components/families/family-form-toolbar';

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
            <Form
                action={`/families/${family.id}`}
                method="put"
                className="flex h-full min-h-0 flex-1 flex-col overflow-hidden"
            >
                {({ processing, errors }) => (
                    <>
                        <FamilyFormToolbar
                            title="تعديل عائلة"
                            processing={processing}
                            saveLabel="حفظ التعديل"
                        />
                        <FamilyFormDocument
                            heading="استمارة تعديل عائلة"
                            description="راجع بيانات العائلة حسب الأقسام ثم احفظ التعديل"
                        >
                            <FamilyFormFields
                                errors={errors}
                                values={family}
                            />
                        </FamilyFormDocument>
                    </>
                )}
            </Form>
        </>
    );
}

EditFamily.layout = {
    breadcrumbs: [],
};
