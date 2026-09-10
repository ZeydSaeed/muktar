import {
    ArchiveRestore,
    BookUser,
    DatabaseBackup,
    FileSpreadsheet,
    Trash2,
    UserPlus,
} from 'lucide-react';
import type { NavItem } from '@/types';

export const mainNavItems: NavItem[] = [
    {
        title: 'اضافة عائلة',
        href: '/families/create',
        icon: UserPlus,
    },
    {
        title: 'حذف عائلة',
        href: '/families/delete',
        icon: Trash2,
    },
    {
        title: 'سجل العوائل',
        href: '/families',
        icon: BookUser,
    },
    {
        title: 'استيراد من اكسل',
        href: '/data/excel-import',
        icon: FileSpreadsheet,
    },
    {
        title: 'نسخ احتياطي',
        href: '/data/backup',
        icon: DatabaseBackup,
    },
    {
        title: 'استرداد البيانات',
        href: '/data/restore',
        icon: ArchiveRestore,
    },
];
