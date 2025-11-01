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
                new ContentType(0, 'Şubelerimiz', 'subelerimiz', 'subdirectory-arrow-right', Types::other, false),
                new ContentType(0, 'Şubelerimiz', 'subelerimiz', 'subdirectory-arrow-right', Types::other, false),
                new ContentType(0, 'Şubelerimiz', 'subelerimiz', 'subdirectory-arrow-right', Types::other, false),
                new ContentType(0, 'Şubelerimiz', 'subelerimiz', 'subdirectory-arrow-right', Types::other, false),
                new ContentType(0, 'Şubelerimiz', 'subelerimiz', 'subdirectory-arrow-right', Types::other, false),
                new ContentType(0, 'Şubelerimiz', 'subelerimiz', 'subdirectory-arrow-right', Types::other, false),
                new ContentType(0, 'Şubelerimiz', 'subelerimiz', 'subdirectory-arrow-right', Types::other, false),

            ]
        ];
    }
}


/*
Tekstil
Tatil & Turizm
Eğitim
Hizmet
Sağlık
Yemek
Tekstil & Giyim
Eğlence
Cafe & Rastaurant
Spor
Otomobil
Kuaför & Güzellik
Çiçek
Kitap & Kırtasiye
Baklava dünyası
Tatil
*/