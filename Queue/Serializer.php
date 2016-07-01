<?php

namespace Dugun\QueueBundle\Queue;

/**
 * Class Serializer.
 *
 * @author Farhad Safarov <farhad.safarov@gmail.com>
 */
class Serializer
{
    public static function serialize($value)
    {
        return base64_encode(serialize($value));
    }

    public static function unserialize($string)
    {
        return unserialize(base64_decode($string));
    }
}
