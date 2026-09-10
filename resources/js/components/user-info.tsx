import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { useInitials } from '@/hooks/use-initials';
import { cn } from '@/lib/utils';
import type { User } from '@/types';

export function UserInfo({
    user,
    showEmail = false,
    className,
}: {
    user: User;
    showEmail?: boolean;
    className?: string;
}) {
    const getInitials = useInitials();

    return (
        <div
            className={cn(
                'flex min-w-0 flex-1 items-center gap-2 text-right',
                className,
            )}
        >
            <Avatar className="h-8 w-8 shrink-0 overflow-hidden rounded-full">
                <AvatarImage src={user.avatar} alt={user.name} />
                <AvatarFallback className="bg-brand-gold/20 text-brand-navy rounded-lg">
                    {getInitials(user.name)}
                </AvatarFallback>
            </Avatar>
            <div className="grid min-w-0 flex-1 text-right text-sm leading-tight group-data-[collapsible=icon]:hidden">
                <span className="truncate font-medium">{user.name}</span>
                {showEmail && (
                    <span className="text-muted-foreground truncate text-xs">
                        {user.email}
                    </span>
                )}
            </div>
        </div>
    );
}
