<?php

namespace OroCRM\Bundle\MagentoBundle\ImportExport\Serializer;

use Oro\Bundle\ImportExportBundle\Field\FieldHelper;
use Oro\Bundle\ImportExportBundle\Serializer\Normalizer\ConfigurableEntityNormalizer;

use OroCRM\Bundle\MagentoBundle\Entity\Cart;
use OroCRM\Bundle\MagentoBundle\Provider\MagentoConnectorInterface;
use OroCRM\Bundle\MagentoBundle\Service\ImportHelper;

class CartNormalizer extends ConfigurableEntityNormalizer
{
    /**
     * @var ImportHelper
     */
    protected $importHelper;

    /**
     * @param FieldHelper $fieldHelper
     * @param ImportHelper $contextHelper
     */
    public function __construct(FieldHelper $fieldHelper, ImportHelper $contextHelper)
    {
        parent::__construct($fieldHelper);
        $this->importHelper = $contextHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null, array $context = array())
    {
        return $type == MagentoConnectorInterface::CART_TYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = array())
    {
        if (!empty($data['billingAddress'])) {
            $data['billingAddress'] = $this->importHelper->getFixedAddress($data['billingAddress']);
        }
        if (!empty($data['shippingAddress'])) {
            $data['shippingAddress'] = $this->importHelper->getFixedAddress($data['shippingAddress']);
        }
        if (!empty($data['paymentDetails'])) {
            $data['paymentDetails'] = $this->importHelper->denormalizePaymentDetails($data['paymentDetails']);
        }

        /** @var Cart $cart */
        $cart = parent::denormalize($data, $class, $format, $context);

        $channel = $this->importHelper->getChannelFromContext($context);
        $cart->setChannel($channel);
        if ($cart->getStore()) {
            $cart->getStore()->setChannel($channel);
        }

        if (!empty($data['email'])) {
            $cart->getCustomer()->setEmail($data['email']);
        }

        return $cart;
    }
}
