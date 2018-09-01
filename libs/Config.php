<?php
namespace Libs;
/**
 * Config Class
 *
 * Config class reads all environment related configurations from env.php
 */
class Config
{
    /***
     * Configuration array
     *
     * @var null
     */
    protected static $configArray = null;
    /***
     * @param $configPath
     */
    static function load($configPath)
    {
        self::$configArray = include($configPath);
    }
    /***
     * Get specific configuration
     *
     * @param $configKey
     * @param null $default
     * @return null
     */
    public static function get( $configKey, $default = null )
    {
        $configKey = explode(".",$configKey);
        $value = self::$configArray;
        foreach( $configKey as $subKey ) {
            if( array_key_exists($subKey,$value) ) {
                $value = $value[$subKey];
            } else {
                $value = $default;
            }
        }
        return $value;
    }
}