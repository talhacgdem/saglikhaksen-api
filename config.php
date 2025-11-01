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
                new ContentType(25, 'Duyuru & Gündem', 'duyurular', 'campaign', Types::category, true),
                new ContentType(29, 'Basında Biz', 'basinda-biz', 'newspaper', Types::category, true),
                new ContentType(28, 'Başvurularımız', 'basvurularimiz', 'assignment', Types::category, true),
                new ContentType(23, 'Haberler', 'haberler', 'article', Types::category, true),
                new ContentType(30, 'Hedeflerimiz', 'hedeflerimiz', 'flag', Types::category, true),
                new ContentType(0, 'Şubelerimiz', 'subelerimiz', 'business', Types::other, false),
                new ContentType(1, 'Eğitim', 'saglik:egitim', 'school', Types::other, false),
                new ContentType(2, 'Hizmet', 'saglik:hizmet', 'build', Types::other, false),
                new ContentType(3, 'Eğlence', 'saglik:eglence', 'celebration', Types::other, false),
                new ContentType(4, 'Restaurant', 'saglik:restaurant', 'restaurant', Types::other, false),
                new ContentType(5, 'Spor', 'saglik:spor', 'sports-soccer', Types::other, false),
                new ContentType(6, 'Otomobil', 'saglik:otomobil', 'directions-car', Types::other, false),
                new ContentType(7, 'Kuaför', 'saglik:kuafor', 'content-cut', Types::other, false),
                new ContentType(8, 'Çiçek', 'saglik:cicek', 'local-florist', Types::other, false),
                new ContentType(9, 'Kırtasiye', 'saglik:kirtasiye', 'edit', Types::other, false),
                new ContentType(10, 'Tatil', 'saglik:tatil', 'beach-access', Types::other, false),
                new ContentType(11, 'Tekstil', 'saglik:tekstil', 'checkroom', Types::other, false),
            ]
        ];
    }
}
