import { Head } from '@inertiajs/react';
import AppearanceTabs from '@/components/appearance-tabs';
import DeleteUser from '@/components/delete-user';
import { NeighborhoodForm } from '@/components/settings/neighborhood-form';
import { PinForm } from '@/components/settings/pin-form';
import { ProfileForm } from '@/components/settings/profile-form';
import { SettingsSection } from '@/components/settings/settings-section';
import { edit } from '@/routes/profile';

type Props = {
    pinLength: number;
};

export default function Profile({ pinLength }: Props) {
    return (
        <>
            <Head title="الإعدادات" />

            <div className="flex h-full flex-col gap-2">
                <SettingsSection title="اسم الحي">
                    <NeighborhoodForm />
                </SettingsSection>

                <SettingsSection title="المظهر" inline>
                    <AppearanceTabs />
                </SettingsSection>

                <SettingsSection title="الملف الشخصي">
                    <ProfileForm />
                </SettingsSection>

                <SettingsSection title="الرقم السري">
                    <PinForm pinLength={pinLength} />
                </SettingsSection>

                <DeleteUser />
            </div>
        </>
    );
}

Profile.layout = {
    breadcrumbs: [
        {
            title: 'الإعدادات',
            href: edit(),
        },
    ],
};
