<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace NF\ExtendProducts\Api;

interface ExtendProductsManagementInterface
{

    /**
     * GET for extendproducts api
     * @param string $match
     * @param string $matchValue
     * @param string $attr
     * @param string $options
     * @param string $frontValues
     * @return string[]
     */
    public function getExtendProducts($match, $matchValue, $attr, $options, $frontValues);
}

