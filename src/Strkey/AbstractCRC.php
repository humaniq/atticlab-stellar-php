<?php

namespace Smartmoney\Stellar\Strkey;

abstract class AbstractCRC implements CRCInterface
{
    /**
     *
     * @var int
     * @internal
     */
    public $checksum;
    /**
     *
     * @var int
     * @internal
     */
    protected $initChecksum = 0x0;
    /**
     *
     * @var int
     * @internal
     */
    public $xorMask = 0x0;

    public function __construct()
    {
        $this->reset();
    }

    public function finish()
    {
        return $this->pack($this->getChecksum());
    }

    public function reset()
    {
        $this->checksum = $this->initChecksum;
    }

    /**
     *
     * @internal
     */
    public function getChecksum()
    {
        return $this->checksum ^ $this->xorMask;
    }

    /**
     *
     * @internal
     */
    abstract protected function pack($checksum);
}