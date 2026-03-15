<?php

declare(strict_types=1);

namespace App\Service\BetCircle\Wallet;

use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductAttributeValueInterface;

final class TokenAmountResolver
{
    public function resolveOrderItemTokens(OrderItemInterface $orderItem): int
    {
        $variant = $orderItem->getVariant();
        if (null === $variant) {
            return 0;
        }

        $product = $variant->getProduct();
        if (!$product instanceof ProductInterface) {
            return 0;
        }

        $tokenAmount = $this->resolveProductTokenAmount($product);

        return $tokenAmount * $orderItem->getQuantity();
    }

    public function resolveProductTokenAmount(ProductInterface $product): int
    {
        foreach ($product->getAttributes() as $attributeValue) {
            if (!$attributeValue instanceof ProductAttributeValueInterface) {
                continue;
            }

            $attribute = $attributeValue->getAttribute();
            if (null === $attribute) {
                continue;
            }

            if ('token_amount' !== $attribute->getCode()) {
                continue;
            }

            $value = $attributeValue->getValue();

            if (is_int($value)) {
                return $value;
            }

            if (is_string($value) && is_numeric($value)) {
                return (int) $value;
            }

            return 0;
        }

        return 0;
    }
}
