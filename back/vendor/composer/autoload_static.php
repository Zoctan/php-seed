<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitcbfea467e759cba3600718835ed968db
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'Predis\\' => 7,
        ),
        'M' => 
        array (
            'Medoo\\' => 6,
        ),
        'L' => 
        array (
            'Lcobucci\\JWT\\' => 13,
            'Lcobucci\\Clock\\' => 15,
        ),
        'F' => 
        array (
            'Faker\\' => 6,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Predis\\' => 
        array (
            0 => __DIR__ . '/..' . '/predis/predis/src',
        ),
        'Medoo\\' => 
        array (
            0 => __DIR__ . '/..' . '/catfan/medoo/src',
        ),
        'Lcobucci\\JWT\\' => 
        array (
            0 => __DIR__ . '/..' . '/lcobucci/jwt/src',
        ),
        'Lcobucci\\Clock\\' => 
        array (
            0 => __DIR__ . '/..' . '/lcobucci/clock/src',
        ),
        'Faker\\' => 
        array (
            0 => __DIR__ . '/..' . '/fzaninotto/faker/src/Faker',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitcbfea467e759cba3600718835ed968db::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitcbfea467e759cba3600718835ed968db::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitcbfea467e759cba3600718835ed968db::$classMap;

        }, null, ClassLoader::class);
    }
}
