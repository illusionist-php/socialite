<?php

namespace Illusionist\Socialite\Facades;

use Illuminate\Support\Facades\Facade;
use Illusionist\Socialite\Contracts\Factory;

/**
 * @method static \Illusionist\Socialite\Contracts\Provider driver(string $driver = null)
 *
 * @see \Illusionist\Socialite\SocialiteManager
 */
class Socialite extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Factory::class;
    }
}
