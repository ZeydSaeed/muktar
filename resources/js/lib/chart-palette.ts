import { brandColors } from '@/lib/brand-colors';

export const chartPalette = [
    brandColors.navy,
    brandColors.green,
    brandColors.gold,
] as const;

export function chartColor(index: number): string {
    return chartPalette[index % chartPalette.length];
}
