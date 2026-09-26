import { Form, Head } from '@inertiajs/react';
import { Upload } from 'lucide-react';
import { DataToolPanel } from '@/components/data/data-tool-panel';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';

type Props = {
    families_count: number;
    children_count: number;
};

export default function RestorePage({
    families_count,
    children_count,
}: Props) {
    return (
        <>
            <Head title="استيراد النسخ الاحتياطي" />
            <DataToolPanel
                title="استيراد النسخ الاحتياطي"
                description="يستبدل جميع العوائل والأولاد الحالية بمحتوى ملف النسخة الاحتياطية."
            >
                <p className="text-sm">
                    سيتم حذف {families_count} عائلة و{children_count} ابناً واستبدالهم بالملف.
                </p>

                <Form
                    action="/data/restore"
                    method="post"
                    encType="multipart/form-data"
                    className="space-y-4"
                >
                    {({ processing, errors }) => (
                        <>
                            <div className="grid gap-2">
                                <Label htmlFor="file">ملف النسخة الاحتياطية</Label>
                                <Input
                                    id="file"
                                    name="file"
                                    type="file"
                                    required
                                    accept=".json,application/json"
                                />
                                <InputError message={errors.file} />
                            </div>
                            <label className="flex items-start gap-2 text-sm">
                                <input
                                    type="checkbox"
                                    name="confirm"
                                    value="1"
                                    required
                                    className="mt-1 size-4 accent-primary"
                                />
                                أؤكد استبدال البيانات الحالية
                            </label>
                            <InputError message={errors.confirm} />
                            <Button
                                type="submit"
                                variant="destructive"
                                disabled={processing}
                            >
                                {processing ? (
                                    <Spinner />
                                ) : (
                                    <Upload className="size-4" />
                                )}
                                استيراد النسخ الاحتياطي
                            </Button>
                        </>
                    )}
                </Form>
            </DataToolPanel>
        </>
    );
}

RestorePage.layout = {
    breadcrumbs: [],
};
