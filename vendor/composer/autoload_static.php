<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitb52071668d01313d11f35ce53c513771
{
    public static $files = array (
        '9c9a81795c809f4710dd20bec1e349df' => __DIR__ . '/..' . '/joshcam/mysqli-database-class/MysqliDb.php',
        '94df122b6b32ca0be78d482c26e5ce00' => __DIR__ . '/..' . '/joshcam/mysqli-database-class/dbObject.php',
    );

    public static $prefixesPsr0 = array (
        'S' => 
        array (
            'Slim' => 
            array (
                0 => __DIR__ . '/..' . '/slim/slim',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixesPsr0 = ComposerStaticInitb52071668d01313d11f35ce53c513771::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}