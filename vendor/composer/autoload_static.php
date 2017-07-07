<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit005fff4529fad1a74a6d28e7b1855924
{
    public static $prefixLengthsPsr4 = array (
        'D' => 
        array (
            'DocuSign\\eSign\\' => 15,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'DocuSign\\eSign\\' => 
        array (
            0 => __DIR__ . '/..' . '/docusign/esign-client/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit005fff4529fad1a74a6d28e7b1855924::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit005fff4529fad1a74a6d28e7b1855924::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
