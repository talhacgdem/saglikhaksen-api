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
    new ContentType(25, 'Duyuru & Gündem', 'duyurular', 'campaign', Types::category, true), // notifications -> campaign
    new ContentType(29, 'Basında Biz', 'basinda-biz', 'newspaper', Types::category, true), // feed -> newspaper
    new ContentType(28, 'Başvurularımız', 'basvurularimiz', 'assignment', Types::category, true), // notifications -> assignment
    new ContentType(23, 'Haberler', 'haberler', 'article', Types::category, true), // article (zaten uygun)
    new ContentType(30, 'Hedeflerimiz', 'hedeflerimiz', 'flag', Types::category, true), // notifications -> flag
    new ContentType(0, 'Tekstil', 'saglik:tekstil', 'checkroom', Types::other, false), // subdirectory-arrow-right -> checkroom
    new ContentType(1, 'Eğitim', 'saglik:egitim', 'school', Types::other, false), // subdirectory-arrow-right -> school
    new ContentType(2, 'Hizmet', 'saglik:hizmet', 'build', Types::other, false), // subdirectory-arrow-right -> build
    new ContentType(3, 'Eğlence', 'saglik:eglence', 'celebration', Types::other, false), // subdirectory-arrow-right -> celebration
    new ContentType(4, 'Restaurant', 'saglik:restaurant', 'restaurant', Types::other, false), // subdirectory-arrow-right -> restaurant
    new ContentType(5, 'Spor', 'saglik:spor', 'sports-soccer', Types::other, false), // subdirectory-arrow-right -> sports-soccer
    new ContentType(6, 'Otomobil', 'saglik:otomobil', 'directions-car', Types::other, false), // subdirectory-arrow-right -> directions-car
    new ContentType(7, 'Kuaför', 'saglik:kuafor', 'content-cut', Types::other, false), // subdirectory-arrow-right -> content-cut
    new ContentType(8, 'Çiçek', 'saglik:cicek', 'local-florist', Types::other, false), // subdirectory-arrow-right -> local-florist
    new ContentType(9, 'Kırtasiye', 'saglik:kirtasiye', 'edit', Types::other, false), // subdirectory-arrow-right -> edit
    new ContentType(10, 'Tatil', 'saglik:tatil', 'beach-access', Types::other, false), // subdirectory-arrow-right -> beach-access
]
        ];
    }
}
