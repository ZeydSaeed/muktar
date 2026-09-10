export const brandColors = {
    navy: '#0B1849',
    green: '#124D1C',
    gold: '#E4B028',
    cream: '#EBEDE3',
} as const;

export type BrandColor = keyof typeof brandColors;
