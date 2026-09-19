import { Form, Head } from '@inertiajs/react';
import { FamilyFormDocument } from '@/components/families/family-form-document';
import { FamilyFormFields } from '@/components/families/family-form-fields';
import { FamilyFormToolbar } from '@/components/families/family-form-toolbar';

export default function CreateFamily() {
    return (
        <>
            <Head title="اضافة عائلة" />
            <Form
                action="/families"
                method="post"
                className="flex h-full min-h-0 flex-1 flex-col overflow-hidden"
            >
                {({ processing, errors }) => (
                    <>
                        <FamilyFormToolbar
                            title="اضافة عائلة"
                            processing={processing}
                        />
                        <FamilyFormDocument
                            heading="استمارة إضافة عائلة"
                            description="عبّئ الحقول حسب الأقسام ثم احفظ الاستمارة"
                        >
                            <FamilyFormFields errors={errors} />
                        </FamilyFormDocument>
                    </>
                )}
            </Form>
        </>
    );
}

CreateFamily.layout = {
    breadcrumbs: [],
};
