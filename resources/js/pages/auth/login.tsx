import { Form, Head } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';
import AppLogoIcon from '@/components/layout/app-logo-icon';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';

export default function Login() {
    return (
        <>
            <Head title="دخول النظام" />

            <div
                dir="rtl"
                className="bg-brand-cream flex min-h-svh flex-col items-center justify-center px-6 py-12 sm:px-10"
            >
                <div className="w-full max-w-md space-y-8">
                    <div className="space-y-3 text-center">
                        <div className="bg-brand-navy mx-auto flex size-16 items-center justify-center rounded-2xl shadow-lg">
                            <AppLogoIcon className="text-brand-gold size-9" />
                        </div>

                        <div className="space-y-2">
                            <h1 className="text-brand-navy text-3xl font-bold tracking-tight">
                                مرحباً، مختار المنطقة
                            </h1>
                            <p className="text-brand-navy/65 text-base leading-relaxed">
                                اضغط للدخول إلى لوحة التحكم وإدارة السجل السكني
                                لمنطقتك
                            </p>
                        </div>
                    </div>

                    <div className="grid grid-cols-3 gap-3">
                        {[
                            { label: 'العائلات', value: 'سجل كامل' },
                            { label: 'المنطقة', value: 'متابعة دقيقة' },
                            { label: 'النظام', value: 'آمن وسريع' },
                        ].map((item) => (
                            <div
                                key={item.label}
                                className="border-brand-navy/10 rounded-xl border bg-white/60 px-3 py-4 text-center"
                            >
                                <p className="text-brand-navy text-sm font-semibold">
                                    {item.label}
                                </p>
                                <p className="text-brand-green mt-1 text-xs">
                                    {item.value}
                                </p>
                            </div>
                        ))}
                    </div>

                    <Form action="/enter" method="post" className="w-full">
                        {({ processing }) => (
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
