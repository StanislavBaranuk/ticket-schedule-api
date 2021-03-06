<?php
/**
 * Created by PhpStorm.
 * User: stanislaw
 * Date: 2/5/19
 * Time: 09:53
 */

/**
 * Class TokenGenerator
 */
class TokenGenerator
{
    /**
     * @return string
     */
    public static function generate (): string {
        $token = '';

        for ($i = 0; $i < 30; $i++) {
            $symbol = [rand(48, 57), rand(97, 122), rand(65, 90)];

            $token .= chr($symbol[rand(0, 2)]);
        }

        return $token;
    }
}