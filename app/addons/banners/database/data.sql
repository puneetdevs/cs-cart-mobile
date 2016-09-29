REPLACE INTO ?:banners (`banner_id`, `status`, `type`, `target`, `localization`, `timestamp`) VALUES(6, 'A', 'G', 'T', '', UNIX_TIMESTAMP(NOW()));
REPLACE INTO ?:banners (`banner_id`, `status`, `type`, `target`, `localization`, `timestamp`) VALUES(7, 'A', 'G', 'T', '', 1421096400);
REPLACE INTO ?:banners (`banner_id`, `status`, `type`, `target`, `localization`, `timestamp`) VALUES(8, 'A', 'G', 'T', '', 1418072400);
REPLACE INTO ?:banners (`banner_id`, `status`, `type`, `target`, `localization`, `timestamp`) VALUES(9, 'A', 'G', 'T', '', 1418072400);

REPLACE INTO ?:images (`image_id`, `image_path`, `image_x`, `image_y`) VALUES(189, 'common_image_1.jpg', 171, 149);
REPLACE INTO ?:images (`image_id`, `image_path`, `image_x`, `image_y`) VALUES(190, 'common_image_2.gif', 171, 170);
REPLACE INTO ?:images (`image_id`, `image_path`, `image_x`, `image_y`) VALUES(233, 'banner_1.jpg', 940, 400);
REPLACE INTO ?:images (`image_id`, `image_path`, `image_x`, `image_y`) VALUES(234, 'banner_2.jpg', 940, 400);
REPLACE INTO ?:images (`image_id`, `image_path`, `image_x`, `image_y`) VALUES(235, 'banner_3.jpg', 940, 400);
REPLACE INTO ?:images (`image_id`, `image_path`, `image_x`, `image_y`) VALUES(1175, 'nokian1.png', 743, 407);
REPLACE INTO ?:images (`image_id`, `image_path`, `image_x`, `image_y`) VALUES(1176, 'gift_certificate.png', 1200, 136);
REPLACE INTO ?:images (`image_id`, `image_path`, `image_x`, `image_y`) VALUES(1177, 'holiday_gift.png', 900, 175);
REPLACE INTO ?:images (`image_id`, `image_path`, `image_x`, `image_y`) VALUES(1186, 'shop_with_easy.png', 433, 407);

REPLACE INTO ?:images_links (`pair_id`, `object_id`, `object_type`, `image_id`, `detailed_id`, `type`, `position`) VALUES(136, 1, 'promo', 189, 0, 'M', 0);
REPLACE INTO ?:images_links (`pair_id`, `object_id`, `object_type`, `image_id`, `detailed_id`, `type`, `position`) VALUES(137, 2, 'promo', 190, 0, 'M', 0);
REPLACE INTO ?:images_links (`pair_id`, `object_id`, `object_type`, `image_id`, `detailed_id`, `type`, `position`) VALUES(177, 3, 'promo', 233, 0, 'M', 0);
REPLACE INTO ?:images_links (`pair_id`, `object_id`, `object_type`, `image_id`, `detailed_id`, `type`, `position`) VALUES(178, 4, 'promo', 234, 0, 'M', 0);
REPLACE INTO ?:images_links (`pair_id`, `object_id`, `object_type`, `image_id`, `detailed_id`, `type`, `position`) VALUES(179, 5, 'promo', 235, 0, 'M', 0);
REPLACE INTO ?:images_links (`pair_id`, `object_id`, `object_type`, `image_id`, `detailed_id`, `type`, `position`) VALUES(1059, 14, 'promo', 1175, 0, 'M', 0);
REPLACE INTO ?:images_links (`pair_id`, `object_id`, `object_type`, `image_id`, `detailed_id`, `type`, `position`) VALUES(1062, 17, 'promo', 1176, 0, 'M', 0);
REPLACE INTO ?:images_links (`pair_id`, `object_id`, `object_type`, `image_id`, `detailed_id`, `type`, `position`) VALUES(1064, 19, 'promo', 1177, 0, 'M', 0);
REPLACE INTO ?:images_links (`pair_id`, `object_id`, `object_type`, `image_id`, `detailed_id`, `type`, `position`) VALUES(1076, 22, 'promo', 1186, 0, 'M', 0);