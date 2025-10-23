<?php

require_once __DIR__ . '/dto/ContentType.php';
require_once __DIR__ . '/dto/Types.php';
use dto\ContentType;
use dto\Types;

return [
    "auth" => [
        "username" => "***",
        "password" => "***",
    ],
    "contentTypes" => [
        new ContentType(25, 'Duyuru & Gündem', 'duyurular', 'notifications', Types::category),
        new ContentType(29, 'Basında Biz', 'basinda-biz', 'feed', Types::category),
        new ContentType(28, 'Başvurularımız', 'basvurularimiz', 'notifications', Types::category),
        new ContentType(87, 'Birimlerimiz', 'birimlerimiz', 'notifications', Types::category),
        new ContentType(23, 'Haberler', 'haberler', 'article', Types::category),
        new ContentType(30, 'Hedeflerimiz', 'hedeflerimiz', 'notifications', Types::category),
        new ContentType(1504, 'Üyelerimize Özel', 'uyelere-ozel', 'campaign', Types::page),
        new ContentType(1484, 'Soru-Cevap', 'soru-cevap', 'question_answer', Types::page)
    ]
];