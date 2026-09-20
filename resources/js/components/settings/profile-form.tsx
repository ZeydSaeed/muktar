import { Form, usePage } from '@inertiajs/react';
import { Link } from '@inertiajs/react';
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { Auth } from '@/types';
import { send } from '@/routes/verification';

type PageProps = {
    auth: Auth;
    mustVerifyEmail?: boolean;
    status?: string;
};

export function ProfileForm() {
    const { auth, mustVerifyEmail, status } = usePage<PageProps>().props;

    return (
        <Form
            {...ProfileController.update.form()}
            options={{ preserveScroll: true }}
            className="grid gap-2 sm:grid-cols-[1fr_1fr_auto] sm:items-end"
        >
            {({ processing, errors }) => (
                <>
                    <div className="min-w-0">
                        <Label htmlFor="name">الاسم</Label>
                        <Input
                            id="name"
                            className="mt-1"
                            defaultValue={auth.user.name}
                            name="name"
                            required
                            autoComplete="name"
                            placeholder="الاسم الكامل"
                        />
                        <InputError message={errors.name} />
                    </div>
                    <div className="min-w-0">
                        <Label htmlFor="email">البريد الإلكتروني</Label>
                        <Input
                            id="email"
                            type="email"
                            className="mt-1"
                            defaultValue={auth.user.email}
                            name="email"
                            required
                            autoComplete="username"
                            placeholder="البريد الإلكتروني"
                        />
                        <InputError message={errors.email} />
                    </div>
                    <Button
                        disabled={processing}
                        data-test="update-profile-button"
                        className="shrink-0"
                    >
                        حفظ
                    </Button>
                    {mustVerifyEmail && auth.user.email_verified_at === null && (
                        <p className="text-muted-foreground sm:col-span-3 text-sm">
                            لم يتم التحقق من بريدك.{' '}
                            <Link
                                href={send()}
                                as="button"
                                className="text-foreground underline"
                            >
                                إعادة إرسال التحقق
                            </Link>
                            {status === 'verification-link-sent' && (
                                <span className="text-secondary mr-2">
                                    {' '}
                                    تم إرسال الرابط.
                                </span>
                            )}
                        </p>
                    )}
                </>
            )}
        </Form>
    );
}
