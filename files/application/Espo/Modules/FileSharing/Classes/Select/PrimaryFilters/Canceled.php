<?php

namespace Espo\Modules\FileSharing\Classes\Select\PrimaryFilters;

use Espo\Core\Select\Primary\Filter;
use Espo\ORM\Query\SelectBuilder;

class Canceled implements Filter
{
    public function apply(SelectBuilder $queryBuilder): void
    {
        $queryBuilder->where([
            'status=' => ['Canceled']
          ]);
    }
}
?>
