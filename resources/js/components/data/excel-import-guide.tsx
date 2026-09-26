import { ExcelImportSampleTable } from '@/components/data/excel-import-sample-table';
import type { ExcelImportSheetGuide } from '@/types/data';

type Props = {
    guide: ExcelImportSheetGuide;
};

export function ExcelImportGuide({ guide }: Props) {
    return (
        <div className="flex h-full min-h-0 flex-col">
            <ExcelImportSampleTable
                title={guide.title}
                columns={guide.columns}
                rows={guide.rows}
                roles={guide.roles}
                notes={guide.notes}
            />
        </div>
    );
}
