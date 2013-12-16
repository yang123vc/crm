<?php

namespace OroCRM\Bundle\MagentoBundle\ImportExport\Serializer;

use OroCRM\Bundle\MagentoBundle\Entity\Order;
use OroCRM\Bundle\MagentoBundle\Provider\StoreConnector;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class OrderDenormalizer extends AbstractNormalizer implements DenormalizerInterface
{
    const ORDER_TYPE              = 'OroCRM\Bundle\MagentoBundle\Entity\Order';
    const ADDRESS_COLLECTION_TYPE = 'ArrayCollection<OroCRM\Bundle\MagentoBundle\Entity\OrderAddress>';

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return is_array($data) && $type == self::ORDER_TYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = array())
    {
        $channel = $this->getChannelFromContext($context);
        $data    = is_array($data) ? $data : [];

        $website = $this->serializer
            ->denormalize($data['store']['website'], StoreConnector::WEBSITE_TYPE, $format, $context);
        $website->setChannel($channel);

        $data['store'] = $this->serializer->denormalize($data['store'], StoreConnector::STORE_TYPE, $format, $context);
        $data['store']->setWebsite($website);
        $data['store']->setChannel($channel);

        $data                   = $this->denormalizeCreatedUpdated($data, $format);
        $data['paymentDetails'] = $this->denormalizePaymentDetails($data['paymentDetails']);
        $data['addresses']      = $this->denormalizeObject($data, 'addresses', self::ADDRESS_COLLECTION_TYPE, $format);

        // todo items denormalization
        unset($data['items']);

        $order = new Order();
        $this->fillResultObject($order, $data);

        $order->setChannel($channel);

        return $order;
    }
}
