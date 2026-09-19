import { Form, router, usePage } from '@inertiajs/react';
import { Plus, Save, X } from 'lucide-react';
import { useEffect, useState } from 'react';
import { FamilyChildField } from '@/components/families/family-child-field';
import { FamilyChildrenTable } from '@/components/families/family-children-table';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogTitle,
} from '@/components/ui/dialog';
import { Spinner } from '@/components/ui/spinner';
import type { ChildrenModal, FamilyChildRow } from '@/types';

type Draft = {
    name: string;
    birth_date: string;
    national_id_number: string;
    registry_number: string;
    page_number: string;
    profession: string;
};

type Props = {
    childrenModal: ChildrenModal | null;
};

const emptyDraft = (): Draft => ({
    name: '',
    birth_date: '',
    national_id_number: '',
    registry_number: '',
    page_number: '',
    profession: '',
});

function draftFromChild(child: FamilyChildRow): Draft {
    return {
        name: child.name,
        birth_date: child.birth_date ?? '',
        national_id_number: child.national_id_number ?? '',
        registry_number: child.registry_number?.toString() ?? '',
        page_number: child.page_number?.toString() ?? '',
        profession: child.profession ?? '',
    };
}

function addChild(familyId: number, draft: Draft, onDone: () => void): void {
    router.post(`/families/${familyId}/children`, draft, {
        preserveScroll: true,
        preserveState: true,
        only: ['childrenModal', 'families'],
        onSuccess: onDone,
    });
}

function closeModal(): void {
    const url = new URL(window.location.href);
    url.searchParams.delete('family_children');
    router.get(
        `${url.pathname}${url.search}`,
        {},
        {
            preserveScroll: true,
            preserveState: true,
            only: ['childrenModal', 'families'],
        },
    );
}

export function FamilyChildrenDialog({ childrenModal }: Props) {
    const pageErrors = usePage().props.errors;
    const [editingId, setEditingId] = useState<number | null>(null);
    const [draft, setDraft] = useState<Draft>(emptyDraft);

    useEffect(() => {
        setEditingId(null);
        setDraft(emptyDraft());
    }, [childrenModal?.family_id]);

    if (childrenModal === null) {
        return null;
    }

    const action = editingId
        ? `/families/${childrenModal.family_id}/children/${editingId}`
        : `/families/${childrenModal.family_id}/children`;
    const method = editingId ? 'put' : 'post';

    return (
        <Dialog open onOpenChange={(open) => !open && closeModal()}>
            <DialogContent
                dir="rtl"
                className="max-h-[90vh] overflow-y-auto sm:max-w-4xl [&>button.absolute]:hidden"
            >
                <div className="space-y-4">
                    <Form
                        action={action}
                        method={method}
                        className="space-y-4"
                        options={{ preserveScroll: true }}
                        onSuccess={() => {
                            setEditingId(null);
                            setDraft(emptyDraft());
                        }}
                    >
                        {({ processing, errors }) => {
                            const fieldErrors = { ...pageErrors, ...errors };

                            return (
                                <>
                                <div className="flex items-start justify-between gap-3">
                                    <div>
                                        <DialogTitle>
                                            أولاد {childrenModal.family_name}
                                        </DialogTitle>
                                        <DialogDescription>
                                            أضف وعدّل واحذف الأولاد المرتبطين برب
                                            الأسرة
                                        </DialogDescription>
                                    </div>
                                    <div className="flex items-center gap-2">
                                        <Button
                                            type="button"
                                            variant="secondary"
                                            disabled={processing}
                                            onClick={() =>
                                                addChild(
                                                    childrenModal.family_id,
                                                    draft,
                                                    () => {
                                                        setEditingId(null);
                                                        setDraft(emptyDraft());
                                                    },
                                                )
                                            }
                                        >
                                            <Plus className="size-4" />
                                            اضافة
                                        </Button>
                                        <Button
                                            type="submit"
                                            disabled={processing}
                                        >
                                            {processing ? (
                                                <Spinner />
                                            ) : (
                                                <Save className="size-4" />
                                            )}
                                            حفظ
                                        </Button>
                                        <Button
                                            type="button"
                                            variant="outline"
                                            onClick={closeModal}
                                        >
                                            <X className="size-4" />
                                            اغلاق
                                        </Button>
                                    </div>
                                </div>

                                <div className="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                    <FamilyChildField
                                        label="اسم الابن"
                                        name="name"
                                        value={draft.name}
                                        required
                                        error={fieldErrors.name}
                                        onChange={(value) =>
                                            setDraft({ ...draft, name: value })
                                        }
                                    />
                                    <FamilyChildField
                                        label="التولد"
                                        name="birth_date"
                                        type="date"
                                        value={draft.birth_date}
                                        error={fieldErrors.birth_date}
                                        onChange={(value) =>
                                            setDraft({
                                                ...draft,
                                                birth_date: value,
                                            })
                                        }
                                    />
                                    <FamilyChildField
                                        label="رقم هوية البطاقة الوطنية"
                                        name="national_id_number"
                                        value={draft.national_id_number}
                                        error={fieldErrors.national_id_number}
                                        onChange={(value) =>
                                            setDraft({
                                                ...draft,
                                                national_id_number: value,
                                            })
                                        }
                                    />
                                    <FamilyChildField
                                        label="السجل"
                                        name="registry_number"
                                        type="number"
                                        value={draft.registry_number}
                                        error={fieldErrors.registry_number}
                                        onChange={(value) =>
                                            setDraft({
                                                ...draft,
                                                registry_number: value,
                                            })
                                        }
                                    />
                                    <FamilyChildField
                                        label="الصحيفة"
                                        name="page_number"
                                        type="number"
                                        value={draft.page_number}
                                        error={fieldErrors.page_number}
                                        onChange={(value) =>
                                            setDraft({
                                                ...draft,
                                                page_number: value,
                                            })
                                        }
                                    />
                                    <FamilyChildField
                                        label="المهنة"
                                        name="profession"
                                        value={draft.profession}
                                        error={fieldErrors.profession}
                                        onChange={(value) =>
                                            setDraft({
                                                ...draft,
                                                profession: value,
                                            })
                                        }
                                    />
                                </div>
                            </>
                            );
                        }}
                    </Form>

                    <FamilyChildrenTable
                        familyId={childrenModal.family_id}
                        items={childrenModal.children}
                        onEdit={(child) => {
                            setEditingId(child.id);
                            setDraft(draftFromChild(child));
                        }}
                        onDeleted={(childId) => {
                            if (editingId === childId) {
                                setEditingId(null);
                                setDraft(emptyDraft());
                            }
                        }}
                    />
                </div>
            </DialogContent>
        </Dialog>
    );
}
