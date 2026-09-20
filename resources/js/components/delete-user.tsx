import { Form } from '@inertiajs/react';
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';

export default function DeleteUser() {
    return (
        <div className="border-destructive/20 bg-destructive/5 flex items-center justify-between gap-3 rounded-xl border px-3 py-2">
            <p className="text-destructive text-sm">
                حذف الحساب نهائي ولا يمكن التراجع عنه.
            </p>
            <Dialog>
                <DialogTrigger asChild>
                    <Button
                        variant="destructive"
                        size="sm"
                        data-test="delete-user-button"
                    >
                        حذف الحساب
                    </Button>
                </DialogTrigger>
                <DialogContent>
                    <DialogTitle>هل تريد حذف حسابك؟</DialogTitle>
                    <DialogDescription>
                        بعد حذف الحساب ستُحذف جميع بياناته نهائياً.
                    </DialogDescription>
                    <Form
                        {...ProfileController.destroy.form()}
                        options={{ preserveScroll: true }}
                        resetOnSuccess
                    >
                        {({ resetAndClearErrors, processing }) => (
                            <DialogFooter className="gap-2">
                                <DialogClose asChild>
                                    <Button
                                        variant="secondary"
                                        onClick={() => resetAndClearErrors()}
                                    >
                                        إلغاء
                                    </Button>
                                </DialogClose>
                                <Button
                                    variant="destructive"
                                    disabled={processing}
                                    asChild
                                >
                                    <button
                                        type="submit"
                                        data-test="confirm-delete-user-button"
                                    >
                                        حذف الحساب
                                    </button>
                                </Button>
                            </DialogFooter>
                        )}
                    </Form>
                </DialogContent>
            </Dialog>
        </div>
    );
}
