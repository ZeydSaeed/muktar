import { Form, Head } from '@inertiajs/react';
import { FamilyFormFields } from '@/components/families/family-form-fields';
import { FamilyFormToolbar } from '@/components/families/family-form-toolbar';

export default function CreateFamily() {
    return (
        <>
            <Head title="اضافة عائلة" />
            <Form
                action="/families"
                method="post"
                className="flex h-full flex-1 flex-col gap-4 overflow-y-auto p-4"
            >
                {({ processing, errors }) => (
                    <>
                        <FamilyFormToolbar
                            title="اضافة عائلة"
                            processing={processing}
                        />
                        <FamilyFormFields errors={errors} />
                    </>
                )}
            </Form>
        </>
    );
}

CreateFamily.layout = {
    breadcrumbs: [],
};
