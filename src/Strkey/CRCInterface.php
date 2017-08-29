<?php

namespace Smartmoney\Stellar\Strkey;

interface CRCInterface
{
    public function reset();

    public function update($data);

    public function finish();
}