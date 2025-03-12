<?php
namespace Quantilus\MobiusUser\Model;

use Magento\Framework\Model\AbstractModel;
use Quantilus\MobiusUser\Api\Data\MobiusUserInterface;

class MobiusUser extends AbstractModel implements MobiusUserInterface
{
    const CACHE_TAG = 'quantilus_mobiususer_mobius_user';

    /**
     * Model cache tag for clear cache in after save and after delete
     *
     * @var string
     */
    protected $_cacheTag = self::CACHE_TAG;

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'mobius_user';

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Quantilus\MobiusUser\Model\ResourceModel\MobiusUser::class);
    }

    /**
     * @inheritDoc
     */
    public function getMobiusUserId(){
      return $this->getData(self::MOBIUS_USER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setMobiusUserId($mobiusUserId){
      return $this->setData(self::MOBIUS_USER_ID, $mobiusUserId);
    }

    /**
     * @inheritDoc
     */
    public function getAccountNumber(){
      return $this->getData(self::ACCOUNT_NUMBER);
    }

    /**
     * @inheritDoc
     */
    public function setAccountNumber($accountNumber){
      return $this->setData(self::ACCOUNT_NUMBER, $accountNumber);
    }

    /**
     * @inheritDoc
     */
    public function getEmail(){
      return $this->getData(self::EMAIL);
    }

    /**
     * @inheritDoc
     */
    public function setEmail($email){
      return $this->setData(self::EMAIL, $email);
    }

    /**
     * @inheritDoc
     */
    public function getKitcode(){
      return $this->getData(self::KITCODE);
    }

    /**
     * @inheritDoc
     */
    public function setKitcode($kitcode){
      return $this->setData(self::KITCODE, $kitcode);
    }

    /**
     * Return a unique id for the model.
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}
