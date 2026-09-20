import {
    Column,
    Cypress,
    Fountain,
    TallWindow,
} from '@/components/district-architecture-parts';
import { brandColors } from '@/lib/brand-colors';

const { navy, green, gold, cream } = brandColors;

type Props = {
    className?: string;
};

const upperWindows = [488, 568, 648, 912, 992, 1072];
const lowerWindows = [488, 568, 648, 912, 992, 1072];

export function DistrictArchitectureSvg({ className }: Props) {
    return (
        <svg
            viewBox="0 0 1600 900"
            xmlns="http://www.w3.org/2000/svg"
            className={className}
            preserveAspectRatio="xMidYMid slice"
            aria-hidden="true"
        >
            <rect width="1600" height="900" fill={cream} />
            <circle cx="210" cy="108" r="90" fill={gold} opacity="0.22" />
            <circle cx="210" cy="108" r="50" fill={gold} />

            <rect x="0" y="628" width="1600" height="272" fill={green} />
            <rect x="430" y="712" width="740" height="40" fill={navy} opacity="0.22" />
            <rect x="520" y="628" width="560" height="16" fill={cream} opacity="0.5" />

            <polygon points="430,248 800,108 1170,248" fill={navy} />
            <polygon points="470,248 800,138 1130,248" fill={gold} opacity="0.32" />
            <rect x="450" y="246" width="700" height="14" fill={gold} />

            <rect x="450" y="260" width="700" height="368" fill={cream} />
            <rect x="450" y="608" width="700" height="28" fill={navy} />
            <rect x="688" y="260" width="224" height="376" fill={navy} opacity="0.07" />

            <rect x="700" y="198" width="200" height="62" fill={navy} />
            <polygon points="688,198 800,142 912,198" fill={gold} />
            <rect x="788" y="116" width="24" height="34" fill={navy} />
            <rect x="792" y="104" width="16" height="14" fill={gold} />

            <Column x={724} y={318} height={290} />
            <Column x={764} y={318} height={290} />
            <Column x={836} y={318} height={290} />
            <Column x={876} y={318} height={290} />

            <rect x="744" y="486" width="112" height="122" fill={navy} />
            <rect x="752" y="494" width="44" height="106" fill={gold} opacity="0.88" />
            <rect x="804" y="494" width="44" height="106" fill={gold} opacity="0.88" />
            <rect x="794" y="536" width="12" height="16" fill={cream} />

            <rect x="720" y="606" width="160" height="12" fill={cream} />
            <rect x="704" y="618" width="192" height="12" fill={cream} />
            <rect x="688" y="630" width="224" height="14" fill={cream} />

            {upperWindows.map((x) => (
                <TallWindow key={`u-${x}`} x={x} y={278} width={44} height={90} />
            ))}
            {lowerWindows.map((x) => (
                <TallWindow key={`l-${x}`} x={x} y={458} width={44} height={90} />
            ))}

            <rect x="470" y="400" width="660" height="8" fill={gold} opacity="0.75" />
            <rect x="470" y="408" width="660" height="6" fill={navy} opacity="0.16" />

            <Cypress x={392} y={628} height={230} />
            <Cypress x={424} y={628} height={180} />
            <Cypress x={1176} y={628} height={230} />
            <Cypress x={1208} y={628} height={180} />

            <Fountain cx={800} cy={748} />
        </svg>
    );
}
