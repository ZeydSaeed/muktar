import { Form, Head } from '@inertiajs/react';
import { Download, Upload } from 'lucide-react';
import { ExcelImportGuide } from '@/components/data/excel-import-guide';
import { DataToolPanel } from '@/components/data/data-tool-panel';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import type { ExcelImportSheetGuide } from '@/types/data';

type Props = {
    guide: ExcelImportSheetGuide;
};

export default function ExcelImportPage({ guide }: Props) {
    return (
        <>
            <Head title="استيراد العوائل من اكسل" />
            <DataToolPanel
                title="استيراد العوائل من اكسل"
                description="ملف إكسل واحد. اسم الأرملة أو الزوج (إذا توفيت الزوجة) يُكتب في «اسم رب الاسرة»، والحالة في «اسم الزوجة او الزوج»، ثم الأبناء حتى الصف الفارغ."
                preamble={<ExcelImportGuide guide={guide} />}
            >
                <Button variant="secondary" asChild>
                    <a href="/data/excel-import/template">
                        <Download className="size-4" />
                        تحميل القالب المرجعي
                    </a>
                </Button>

                <Form
                    action="/data/excel-import"
                    method="post"
                    encType="multipart/form-data"
                    className="space-y-4"
                >
                    {({ processing, errors }) => (
                        <>
                            <div className="grid gap-2">
                                <Label htmlFor="file">ملف إكسل</Label>
                                <Input
                                    id="file"
                                    name="file"
                                    type="file"
                                    required
                                    accept=".xlsx,.xls,.csv"
                                />
                                <InputError message={errors.file} />
                            </div>
                            <Button type="submit" disabled={processing}>
                                {processing ? <Spinner /> : <Upload className="size-4" />}
                                استيراد
                            </Button>
                        </>
                    )}
                </Form>
            </DataToolPanel>
        </>
    );
}

ExcelImportPage.layout = {
    breadcrumbs: [],
};
