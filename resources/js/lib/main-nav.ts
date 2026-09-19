import {
    ArchiveRestore,
    BarChart3,
    BookUser,
    DatabaseBackup,
    FileSpreadsheet,
    PieChart,
    Trash2,
    UserPlus,
} from 'lucide-react';
import type { NavItem } from '@/types';

export const mainNavItems: NavItem[] = [
    {
        title: 'سجل العوائل',
        href: '/families',
        icon: BookUser,
    },
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
    {
        title: 'إحصائية بيانية',
        href: '/statistics/charts',
        icon: PieChart,
    },
    {
        title: 'إحصائية',
        href: '/statistics',
        icon: BarChart3,
    },
];
