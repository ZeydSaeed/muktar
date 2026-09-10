<?php

namespace App\Support;

use Illuminate\Http\RedirectResponse;

class ChildrenModalRedirect
{
    public static function backTo(int $familyId): RedirectResponse
    {
        $parts = parse_url(url()->previous()) ?: [];
        parse_str($parts['query'] ?? '', $query);
        $query['family_children'] = $familyId;
        $path = $parts['path'] ?? '/dashboard';

        return redirect()->to($path.'?'.http_build_query($query));
    }
}
