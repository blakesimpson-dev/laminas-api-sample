<?php

declare(strict_types=1);

namespace LaminasApiSample\Domains\Auth;

enum AuthScope: string
{
    case AccountProfile = 'account:profile';
    case AccountItemFilter = 'account:item_filter';
}
