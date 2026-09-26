type Props = {
    title: string;
    notes?: string[];
    columns: string[];
    roles: string[];
    rows: string[][];
};

export function ExcelImportSampleTable({
    title,
    notes = [],
    columns,
    roles,
    rows,
}: Props) {
    return (
        <section className="flex h-full min-h-0 flex-col gap-2">
            <h2 className="shrink-0 font-semibold">ملف «{title}»</h2>
            {notes.map((note) => (
                <p key={note} className="text-muted-foreground shrink-0 text-sm">
                    {note}
                </p>
            ))}
            <div className="excel-import-scroll min-h-0 flex-1 rounded-md border-2 border-primary">
                <table className="min-w-max border-separate border-spacing-0 text-center text-sm">
                    <thead>
                        <tr>
                            <th className="border-primary bg-primary text-primary-foreground sticky top-0 z-20 whitespace-nowrap border px-2 py-2 text-center font-semibold">
                                نوع الصف
                            </th>
                            {columns.map((column) => (
                                <th
                                    key={column}
                                    className="border-primary bg-primary text-primary-foreground sticky top-0 z-20 whitespace-nowrap border px-2 py-2 text-center font-semibold"
                                >
                                    {column}
                                </th>
                            ))}
                        </tr>
                    </thead>
                    <tbody>
                        {rows.map((row, rowIndex) => (
                            <tr
                                key={`${title}-${rowIndex}`}
                                className="even:bg-muted/40"
                            >
                                <td className="border-primary whitespace-nowrap border px-2 py-1.5 text-center align-middle font-medium">
                                    {roles[rowIndex] ?? ''}
                                </td>
                                {row.map((value, cellIndex) => (
                                    <td
                                        key={`${title}-${rowIndex}-${cellIndex}`}
                                        className="border-primary whitespace-nowrap border px-2 py-1.5 text-center align-middle"
                                    >
                                        {value}
                                    </td>
                                ))}
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </section>
    );
}
