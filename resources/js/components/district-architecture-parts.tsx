import { brandColors } from '@/lib/brand-colors';

const { navy, green, gold, cream } = brandColors;

export function Cypress({ x, y, height }: { x: number; y: number; height: number }) {
    return (
        <g>
            <rect x={x - 3} y={y} width={6} height={18} fill={navy} />
            <ellipse cx={x} cy={y - height / 2 + 8} rx={16} ry={height / 2} fill={green} />
            <ellipse
                cx={x}
                cy={y - height / 2 + 18}
                rx={11}
                ry={height / 2.6}
                fill={green}
                opacity="0.75"
            />
        </g>
    );
}

export function TallWindow({
    x,
    y,
    width,
    height,
}: {
    x: number;
    y: number;
    width: number;
    height: number;
}) {
    return (
        <g>
            <rect x={x} y={y} width={width} height={height} fill={navy} />
            <rect
                x={x + 3}
                y={y + 3}
                width={width - 6}
                height={height - 6}
                fill={gold}
                opacity="0.88"
            />
            <rect
                x={x + width / 2 - 1}
                y={y + 3}
                width={2}
                height={height - 6}
                fill={navy}
                opacity="0.45"
            />
            <rect
                x={x + 3}
                y={y + height / 2 - 1}
                width={width - 6}
                height={2}
                fill={navy}
                opacity="0.45"
            />
        </g>
    );
}

export function Column({ x, y, height }: { x: number; y: number; height: number }) {
    return (
        <g>
            <rect x={x - 10} y={y} width={20} height={8} fill={gold} />
            <rect x={x - 7} y={y + 8} width={14} height={height - 16} fill={navy} />
            <rect x={x - 9} y={y + height - 8} width={18} height={8} fill={gold} />
        </g>
    );
}

export function Fountain({ cx, cy }: { cx: number; cy: number }) {
    return (
        <g>
            <ellipse cx={cx} cy={cy} rx={78} ry={22} fill={navy} opacity="0.35" />
            <ellipse cx={cx} cy={cy} rx={58} ry={14} fill={cream} opacity="0.55" />
            <ellipse cx={cx} cy={cy - 8} rx={22} ry={8} fill={gold} />
            <circle cx={cx} cy={cy - 28} r={7} fill={gold} />
        </g>
    );
}
