import { Form, Head } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';
import { DistrictArchitectureSvg } from '@/components/district-architecture-svg';
import InputError from '@/components/input-error';
import AppLogoIcon from '@/components/layout/app-logo-icon';
import PinInput from '@/components/pin-input';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';

type Props = {
    pinLength: number;
};

export default function Login({ pinLength }: Props) {
    return (
        <>
            <Head title="دخول النظام" />

            <div
                dir="rtl"
                className="relative flex min-h-svh items-center justify-start overflow-hidden px-6 py-12 sm:px-10"
            >
                <DistrictArchitectureSvg className="pointer-events-none absolute inset-0 size-full" />
                <div className="bg-brand-navy/10 pointer-events-none absolute inset-0" />

                <div className="border-brand-navy/15 bg-brand-cream/94 relative z-10 w-full max-w-md space-y-8 rounded-3xl border px-8 py-10 shadow-2xl backdrop-blur-sm">
                    <div className="space-y-3 text-center">
                        <div className="bg-brand-navy mx-auto flex size-16 items-center justify-center rounded-2xl shadow-lg">
                            <AppLogoIcon className="text-brand-gold size-9" />
                        </div>

                        <div className="space-y-2">
                            <h1 className="text-brand-navy text-3xl font-bold tracking-tight">
                                مرحباً، مختار المنطقة
                            </h1>
                            <p className="text-brand-navy/65 text-base leading-relaxed">
                                أدخل الرقم السري للدخول إلى لوحة التحكم
                            </p>
                        </div>
                    </div>

                    <Form
                        action="/enter"
                        method="post"
                        className="w-full space-y-5"
                    >
                        {({ processing, errors }) => (
                            <>
                                <div className="grid gap-2 text-right">
                                    <Label htmlFor="pin">الرقم السري</Label>
                                    <PinInput
                                        id="pin"
                                        name="pin"
                                        length={pinLength}
                                        autoComplete="current-password"
                                        placeholder={`أدخل ${pinLength} أرقام`}
                                        autoFocus
                                    />
                                    <InputError message={errors.pin} />
                                </div>

                                <Button
                                    type="submit"
                                    size="lg"
                                    disabled={processing}
                                    className="bg-brand-navy hover:bg-brand-navy/90 text-brand-cream h-14 w-full rounded-xl text-base font-semibold shadow-lg transition-all hover:shadow-xl"
                                >
                                    {processing ? (
                                        <Spinner />
                                    ) : (
                                        <>
                                            دخول النظام
                                            <ArrowLeft className="size-5" />
                                        </>
                                    )}
                                </Button>
                            </>
                        )}
                    </Form>

                    <p className="text-brand-navy/45 text-center text-xs">
                        نظام مخصص لمختار المنطقة السكنية — كربلاء
                    </p>
                </div>
            </div>
        </>
    );
}

Login.layout = null;
