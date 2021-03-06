<?php

namespace Oro\Bundle\ContactUsBundle\Migrations\Schema\v2_0;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\EntityExtendBundle\Extend\RelationType;
use Oro\Bundle\MigrationBundle\Migration\Extension\RenameExtension;
use Oro\Bundle\MigrationBundle\Migration\Extension\RenameExtensionAwareInterface;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\InstallerBundle\Migration\UpdateExtendRelationQuery;

class MigrateRelations implements Migration, RenameExtensionAwareInterface
{
    /** @var RenameExtension */
    private $renameExtension;

    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $this->renameActivityTables($schema, $queries);
    }

    /**
     * @param Schema $schema
     * @param QueryBag $queries
     */
    private function renameActivityTables(Schema $schema, QueryBag $queries)
    {
        $extension = $this->renameExtension;

        $extension->renameTable($schema, $queries, 'oro_rel_c3990ba650ef1ed974a995', 'oro_rel_c3990ba650ef1ed91c32d0');
        $queries->addQuery(new UpdateExtendRelationQuery(
            'Oro\Bundle\ActivityListBundle\Entity\ActivityList',
            'Oro\Bundle\ContactUsBundle\Entity\ContactRequest',
            'contact_request_6ca26e76',
            'contact_request_aeac8609',
            RelationType::MANY_TO_MANY
        ));

        // email
        $extension->renameTable($schema, $queries, 'oro_rel_2653537050ef1ed9f9fe7f', 'oro_rel_2653537050ef1ed9f45d78');
        $queries->addQuery(new UpdateExtendRelationQuery(
            'Oro\Bundle\EmailBundle\Entity\Email',
            'Oro\Bundle\ContactUsBundle\Entity\ContactRequest',
            'contact_request_a223cce9',
            'contact_request_4e3a1184',
            RelationType::MANY_TO_MANY
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function setRenameExtension(RenameExtension $renameExtension)
    {
        $this->renameExtension = $renameExtension;
    }
}
