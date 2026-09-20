import type { ComponentProps, Ref } from 'react';
import PasswordInput from '@/components/password-input';

type Props = Omit<ComponentProps<typeof PasswordInput>, 'type'> & {
    length: number;
    ref?: Ref<HTMLInputElement>;
};

export default function PinInput({ length, ...props }: Props) {
    return (
        <PasswordInput
            inputMode="numeric"
            pattern="[0-9]*"
            maxLength={length}
            dir="ltr"
            {...props}
        />
    );
}
