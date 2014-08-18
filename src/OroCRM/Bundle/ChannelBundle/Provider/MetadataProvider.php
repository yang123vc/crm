<?php

namespace OroCRM\Bundle\ChannelBundle\Provider;

use Symfony\Component\Routing\RouterInterface;

use Oro\Bundle\EntityConfigBundle\Config\ConfigInterface;
use Oro\Bundle\EntityExtendBundle\EntityConfig\ExtendScope;
use Oro\Bundle\EntityBundle\Provider\EntityProvider;
use Oro\Bundle\EntityConfigBundle\Config\ConfigManager;
use Oro\Bundle\EntityConfigBundle\Entity\EntityConfigModel;

class MetadataProvider implements MetadataProviderInterface
{
    const ENTITY_EDIT_ROUTE = 'oro_entityconfig_update';
    const ENTITY_VIEW_ROUTE = 'oro_entityconfig_view';

    /** @var SettingsProvider */
    protected $settings;

    /** @var EntityProvider */
    protected $entityProvider;

    /** @var ConfigManager */
    protected $configManager;

    /**
     * @param SettingsProvider $settings
     * @param EntityProvider   $entityProvider
     * @param ConfigManager    $configManager
     * @param RouterInterface  $router
     */
    public function __construct(
        SettingsProvider $settings,
        EntityProvider $entityProvider,
        ConfigManager $configManager,
        RouterInterface $router
    ) {
        $this->settings       = $settings;
        $this->entityProvider = $entityProvider;
        $this->configManager  = $configManager;
        $this->router         = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntitiesMetadata()
    {
        $result = [];

        foreach ($this->getChannelEntities() as $entityName) {
            $entityConfig      = $this->entityProvider->getEntity($entityName, true);
            $extendConfig      = $this->configManager->getProvider('extend')->getConfig($entityName);
            $configEntityModel = $this->configManager->getConfigEntityModel($entityName);

            if ($configEntityModel instanceof EntityConfigModel) {
                $entityConfig = array_merge($entityConfig, $this->getEntityLinks($configEntityModel));
            }

            $result[$entityName] = array_merge($entityConfig, ['type' => $extendConfig->get('owner')]);
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getIntegrationEntities()
    {
        $result = [];

        foreach ($this->settings->getSettings(SettingsProvider::DATA_PATH) as $setting) {
            $integration = isset($setting['belongs_to']['integration']) ? $setting['belongs_to']['integration'] : false;
            if (false !== $integration) {
                $result[$integration]   = isset($result[$integration]) ? $result[$integration] : [];
                $result[$integration][] = $setting['name'];
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    protected function getChannelEntities()
    {
        $customEntities = $this->configManager->getProvider('extend')->map(
            function (ConfigInterface $extendConfig) {
                $isCustom
                    = $extendConfig->is('is_extend')
                    && $extendConfig->get('owner') === ExtendScope::OWNER_CUSTOM
                    && $extendConfig->in('state', [ExtendScope::STATE_ACTIVE, ExtendScope::STATE_UPDATED]);

                return $isCustom ? $extendConfig->getId()->getClassName() : false;
            }
        );
        $customEntities = array_filter($customEntities);

        $entities = array_map(
            function ($setting) {
                return $setting['name'];
            },
            $this->settings->getSettings(SettingsProvider::DATA_PATH)
        );

        return array_unique(array_merge($customEntities, $entities));
    }

    /**
     * @param EntityConfigModel $configEntityModel
     *
     * @return array
     */
    protected function getEntityLinks(EntityConfigModel $configEntityModel)
    {
        return [
            'entity_id' => $configEntityModel->getId(),
            'edit_link' => $this->generateUrl(self::ENTITY_EDIT_ROUTE, ['id' => $configEntityModel->getId()]),
            'view_link' => $this->generateUrl(self::ENTITY_VIEW_ROUTE, ['id' => $configEntityModel->getId()]),
        ];
    }

    /**
     * @param string $route
     * @param array  $parameters
     *
     * @return string The generated URL
     */
    protected function generateUrl($route, $parameters = [])
    {
        return $this->router->generate($route, $parameters);
    }
}
