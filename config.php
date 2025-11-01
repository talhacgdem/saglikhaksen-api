<?php

namespace main;

use dto\ContentType;
use dto\Types;

class Config
{
    public static function getConfig(): array
    {
        return [
            "auth" => [
                "username" => "sendikapp",
                "password" => "W4r*krQyylnILj!Hglqd53vy",
            ],
            "contentTypes" => [
                new ContentType(25, 'Duyuru & Gündem', 'duyurular', 'notifications', Types::category, true),
                new ContentType(29, 'Basında Biz', 'basinda-biz', 'feed', Types::category, true),
                new ContentType(28, 'Başvurularımız', 'basvurularimiz', 'notifications', Types::category, true),
                new ContentType(23, 'Haberler', 'haberler', 'article', Types::category, true),
                new ContentType(30, 'Hedeflerimiz', 'hedeflerimiz', 'notifications', Types::category, true),
                new ContentType(0, 'Tekstil', 'saglik:tekstil', 'subdirectory-arrow-right', Types::other, false),
                new ContentType(0, 'Eğitim', 'saglik:egitim', 'subdirectory-arrow-right', Types::other, false),
                new ContentType(0, 'Hizmet', 'saglik:hizmet', 'subdirectory-arrow-right', Types::other, false),
                new ContentType(0, 'Eğlence', 'saglik:eglence', 'subdirectory-arrow-right', Types::other, false),
                new ContentType(0, 'Restaurant', 'saglik:restaurant', 'subdirectory-arrow-right', Types::other, false),
                new ContentType(0, 'Spor', 'saglik:spor', 'subdirectory-arrow-right', Types::other, false),
                new ContentType(0, 'Otomobil', 'saglik:otomobil', 'subdirectory-arrow-right', Types::other, false),
                new ContentType(0, 'Kuaför', 'saglik:kuafor', 'subdirectory-arrow-right', Types::other, false),
                new ContentType(0, 'Çiçek', 'saglik:cicek', 'subdirectory-arrow-right', Types::other, false),
                new ContentType(0, 'Kırtasiye', 'saglik:kirtasiye', 'subdirectory-arrow-right', Types::other, false),
                new ContentType(0, 'Tatil', 'saglik:tatil', 'subdirectory-arrow-right', Types::other, false),
            ]
        ];
    }
}
