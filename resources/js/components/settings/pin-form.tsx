import { Form } from '@inertiajs/react';
import { useRef } from 'react';
import SecurityController from '@/actions/App/Http/Controllers/Settings/SecurityController';
import InputError from '@/components/input-error';
import PinInput from '@/components/pin-input';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';

type Props = {
    pinLength: number;
};

export function PinForm({ pinLength }: Props) {
    const currentPinInput = useRef<HTMLInputElement>(null);
    const pinInput = useRef<HTMLInputElement>(null);

    return (
        <Form
            {...SecurityController.update.form()}
            options={{ preserveScroll: true }}
            resetOnError
            resetOnSuccess
            onError={(errors) => {
                if (errors.current_pin) {
                    currentPinInput.current?.focus();
                }

                if (errors.pin) {
                    pinInput.current?.focus();
                }
            }}
            className="grid gap-2 sm:grid-cols-3 sm:items-end"
        >
            {({ errors, processing }) => (
                <>
                    <div className="min-w-0">
                        <Label htmlFor="current_pin">الحالي</Label>
                        <PinInput
                            id="current_pin"
                            name="current_pin"
                            length={pinLength}
                            ref={currentPinInput}
                            autoComplete="current-password"
                            placeholder={`${pinLength} أرقام`}
                            className="mt-1"
                        />
                        <InputError message={errors.current_pin} />
                    </div>
                    <div className="min-w-0">
                        <Label htmlFor="pin">الجديد</Label>
                        <PinInput
                            id="pin"
                            name="pin"
                            length={pinLength}
                            ref={pinInput}
                            autoComplete="new-password"
                            placeholder={`${pinLength} أرقام`}
                            className="mt-1"
                        />
                        <InputError message={errors.pin} />
                    </div>
                    <div className="flex min-w-0 items-end gap-2">
                        <div className="min-w-0 flex-1">
                            <Label htmlFor="pin_confirmation">التأكيد</Label>
                            <PinInput
                                id="pin_confirmation"
                                name="pin_confirmation"
                                length={pinLength}
                                autoComplete="new-password"
                                placeholder={`${pinLength} أرقام`}
                                className="mt-1"
                            />
                            <InputError message={errors.pin_confirmation} />
                        </div>
                        <Button
                            disabled={processing}
                            data-test="update-pin-button"
                            className="shrink-0"
                        >
                            حفظ
                        </Button>
                    </div>
                </>
            )}
        </Form>
    );
}
