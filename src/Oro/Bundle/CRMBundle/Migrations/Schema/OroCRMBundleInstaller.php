<?php

namespace Oro\Bundle\CRMBundle\Migrations\Schema;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\CRMBundle\Migrations\Schema\v2_0\MigrateRelations as MigrateRelations_2_0;
use Oro\Bundle\MigrationBundle\Migration\Installation;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class OroCRMBundleInstaller implements Installation
{
    /**
     * {@inheritdoc}
     */
    public function getMigrationVersion()
    {
        return 'v2_0';
    }

    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        MigrateRelations_2_0::updateWorkFlow($schema, $queries);
    }
}
