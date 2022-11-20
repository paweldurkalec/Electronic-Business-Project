<?php

namespace FreshMail;

class FollowUps
{
    const NUM_FOLLOW_UPS = 3;

    const CONFIG_KEY_ACTIVE = 'fm_follow_up';

    const CONFIG_KEY_SEND_AFTER = 'fm_follow_up_send_after';

    const CONFIG_KEY_DISCOUNT_TYPE = 'fm_follow_up_discount_type';

    const CONFIG_KEY_DISCOUNT_PERCENT = 'fm_follow_up_discount_percent';

    const CONFIG_KEY_DISCOUNT_CODE = 'fm_follow_up_discount_code';

    const CONFIG_KEY_DISCOUNT_LIFETIME = 'fm_follow_up_discount_lifetime';

    const DISCOUNT_NONE = 'none';

    const DISCOUNT_PERCENT = 'percent';

    const DISCOUNT_CODE = 'code';

    public static function getActive($id_shop)
    {
        $active = [];
        for($idFollowUp=1; $idFollowUp<=(int)self::NUM_FOLLOW_UPS; $idFollowUp++){
            if(\Configuration::get(\FreshMail\FollowUps::CONFIG_KEY_ACTIVE.$idFollowUp, null, null, $id_shop)){
                $active[] = $idFollowUp;
            }
        }

        return $active;
    }
}