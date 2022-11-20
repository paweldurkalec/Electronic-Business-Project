<?php

namespace FreshMail;

class ProductNotifications
{
    const DISCOUNT = 'discount';

    const AVAILABLE = 'available';

    const CONFIG_KEY_DISCOUNT = 'fm_product_discount_notify';

    const CONFIG_KEY_AVAILABLE = 'fm_product_available_notify';

    const CONFIG_KEY_DISCOUNT_TRIGGER = 'fm_product_available_notify_trigger';

    const CONFIG_KEY_DISCOUNT_PROMPT = 'fm_product_discount_notify_prompt';

    const CONFIG_KEY_AVAILABLE_PROMPT = 'fm_product_available_notify_prompt';

    public static function isSupportedType($type)
    {
        return in_array($type,
            [
                self::AVAILABLE,
                self::DISCOUNT
            ]
        );
    }
}