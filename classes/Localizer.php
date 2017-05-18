<?php
/**
 * Created by Andre Haralevi
 * Date: 07.11.13
 * Time: 03:28
 */

namespace Photocommunity\Mobile;

class Localizer
{
    public static $loc;
    public static $cat_names;

    public static $col_port_index_body = '';
    public static $col_port_menu_group_name = '';
    public static $col_port_title = '';
    public static $col_port_subtitle = '';
    public static $col_port_seo_title = '';
    public static $col_port_seo_desc = '';
    public static $col_port_seo_keys = '';

    public static $tbl_ds_comments;
    public static $col_auth_name;
    public static $col_auth_mood;
    public static $col_ph_name;
    public static $col_serie_name;
    public static $col_album_name;
    public static $col_comm_text;
    public static $col_ph_comm_cnt;
    public static $col_comp_name;

    private static $cat_names_ru = array(
        100 => 'Человек',
        101 => 'Гламурный портрет',
        102 => 'Классический портрет',
        103 => 'Спонтанный портрет',
        104 => 'Экспрессивный портрет',
        105 => 'Эмоциональный портрет',
        106 => 'Автопортрет',
        107 => 'Дети, младенцы',
        108 => 'Фэшн портрет',
        109 => 'Фетиш портрет',
        110 => 'Пин-ап',
        111 => 'Ню',

        200 => 'Природа и животные',
        201 => 'Пейзажи',
        202 => 'Цветы, растения',
        203 => 'Природные явления',
        204 => 'Домашние животные',
        205 => 'Беспозвоночные',
        206 => 'Рептилии',
        207 => 'Птицы',
        208 => 'Подводный мир',
        209 => 'Дикие животные',
        210 => 'Геология',
        211 => 'Пейзажная панорама',

        300 => 'Город и деревня',
        301 => 'Жанровое фото',
        302 => 'Городская жизнь',
        303 => 'Деревенская жизнь',
        304 => 'Городские виды',
        305 => 'Индастриал',
        306 => 'Мосты',
        307 => 'Заброшенные сооружения',
        308 => 'Парки, сады',
        309 => 'Транспорт',
        310 => 'Городская панорама',

        400 => 'Архитектура',
        401 => 'Экстерьеры',
        402 => 'Интерьеры',
        403 => 'Памятники',
        404 => 'Храмы',

        500 => 'Натюрморт',
        501 => 'Растения, цветы',
        502 => 'Еда, напитки',
        503 => 'Куклы, фигурки',
        504 => 'Прочие предметы',

        600 => 'Макросъёмка',
        601 => 'Предметы',
        602 => 'Природа',
        603 => 'Человек',

        700 => 'Репортаж',
        701 => 'Люди',
        703 => 'Выступления, шоу',
        704 => 'Собрания, события',
        705 => 'Спорт',
        706 => 'Политика',
        707 => 'Военные',
        708 => 'Документальная съемка',
        709 => 'Прочий репортаж',

        800 => 'Коммерческое фото',
        801 => 'Продукты',
        802 => 'Услуги',
        803 => 'Мода',
        804 => 'Прочее коммерческое фото',

        900 => 'Прочие категории',
        901 => 'Концептуальное фото',
        902 => 'Ужасы',
        903 => 'Абстракционизм',
        904 => 'Сюрреализм',
        905 => 'Треш',
        906 => 'Жизнь вокруг',
        907 => '3D-Фото',

        1000 => 'Digital art',
        1001 => '3D-дизайн',
        1002 => 'Фото арт',
        1003 => 'Коллаж',
        1004 => 'Веб-дизайн',
        1005 => 'Логотип',
        1006 => 'Плакат',
        1007 => 'Обложка',
        1008 => 'Графика',
        1009 => 'Аниме',
        1010 => 'Открытка',

        1100 => 'Свадебное фото',
        1101 => 'Свадебный репортаж',
        1102 => 'Свадебная постановка',
        1103 => 'Свадебный портрет',

        1200 => 'Художественное фото',
        1201 => 'Художественное фото',

        2200 => 'Вне конкурса',
        2201 => 'Фото приколы',
        2202 => 'Фото загадка',
        2203 => 'Лица клуба',
        2204 => 'Ретро фото',
        2205 => 'Домашнее фото',

        2300 => 'Портфолио'
    );

    private static $loc_com = array(
        'main_title_loc' => 'Photo community',
        'logout_loc' => 'Sing out',
        'login_short_loc' => 'E-mail',
        'pass_loc' => 'Password',
        'enter_short_loc' => 'Sign in',
        'profile_title_loc' => 'My profile',
        'comm_loc' => 'Comments',
        'comm_del_by_admin_loc' => 'This comment was deleted by the administrator',
        'comm_del_by_author_loc' => 'This comment was deleted by the author',
        'recomm_works_loc' => 'Recommended photos',
        'all_works_loc' => 'All photos',
        'special_works_loc' => 'Special photos',
        'popular_loc' => 'Popular photos',
        'critique_works_loc' => 'Photos for critique',
        'works_of_day_loc' => 'Photos of the day',
        'friends_works_loc' => 'Friend\'s photos',
        'fav_auth_works_loc' => 'Following',
        'auth_works_you_follow_loc' => 'Photos by authors that i follow',
        'works_loc' => 'Photos',
        'rating_loc' => 'Rating',
        'recs_loc' => 'Recommendations',
        'add_rec_loc' => 'Recommend',
        'already_rec_note_loc' => 'Recommendation<br />is received',
        'add_comm_loc' => 'Add comment',
        'wrong_login_pass_short_loc' => 'Wrong E-mail or password',
        'site_full_ver_loc' => 'Desktop version',
        'anonymous_loc' => 'Anonymously',
        'author_loc' => 'Author',
        'limit_recs_achieved_1_loc' => 'Limit of',
        'limit_recs_achieved_2_loc' => 'recommendations per day is achieved',
        'add_favorite_loc' => 'Follow',
        'del_favorite_loc' => 'Unfollow',
        'forbidden_write_comm_loc' => 'Author has disabled comments to this photo',
        'portfolio_loc' => 'Portfolio',
        'without_name' => 'Untitled',
        'success_unsubscribe' => 'You have successfully unsubscribed from newsletter',
        'success_unsubscribe_top' => 'You have successfully unsubscribed from newsletter about most popuar photos and informative comments',
        'author_blocked_loc' => 'Author or this photo is temporary blocked',
        'access_nu_cat_loc' => 'Access to "Artistic Nude" category',
        'add_to_fineart' => 'Fine-art',
        'remove_from_fineart' => 'Not fine-art',

        'smile_loc' => 'Smile',
	    'wink_loc' => 'Wink',
	    'photo_smile_loc' => 'Photo',
	    'super_loc' => 'Super',
	    'beer_loc' => 'Beer',
	    'eek_loc' => 'Eek',
	    'evil_loc' => 'Devil',
	    'curve_smile_loc' => 'Curvy smile',
	    'love_loc' => 'Love',
	    'naezd_loc' => 'Polemic',
	    'lol_loc' => 'LOL',
	    'rolleyes_loc' => 'Roll eyes',
	    'insane_loc' => 'Misunderstanding',
	    'znaika_loc' => 'I know what i say',
	    'nopets_loc' => 'No pets please',
	    'molotok_loc' => 'Hammer',
	    'lamer_loc' => 'Lamer',
	    'redtongue_loc' => 'Red tongue',
	    'idea_loc' => 'Idea',
	    'confused_loc' => 'Confused',
	    'dontlike_loc' => 'Don\'t like',
	    'moderator_loc' => 'Don\'t agree',
	    'shuffle_loc' => 'Shuffle',
	    'frown_loc' => 'Frown',
	    'weep_loc' => 'Weep',
	    'roof_loc' => 'Roof',
	    'hair_loc' => 'Hair',
	    'puke_loc' => 'Puke',
	    'buzz_loc' => 'Break your camera',
	    'taz_loc' => 'Clatter',
    );

    private static $cat_names_com = array(
        '100' => 'People & Portraits',
        '101' => 'Glamour portraits',
        '102' => 'Classic portraits',
        '103' => 'Spontaneous portraits',
        '104' => 'Expressive portraits',
        '105' => 'Emotive portraits',
        '106' => 'Self-portraits',
        '107' => 'Children, infants',
        '108' => 'Fashion portraits',
        '109' => 'Fetish portraits',
        '110' => 'Pin-up',
        '111' => 'Artistic Nude',
        '200' => 'Nature & Animals',
        '201' => 'Landscapes',
        '202' => 'Flowers, plants',
        '203' => 'Nature phenomena',
        '204' => 'Domesticated animals',
        '205' => 'Invertebrates',
        '206' => 'Reptiles, amphibians',
        '207' => 'Birds',
        '208' => 'Aquatic life',
        '209' => 'Wild life',
        '210' => 'Geology',
        '211' => 'Landscape panorama',
        '300' => 'Urban & Rural',
        '301' => 'Street photography',
        '302' => 'City life',
        '303' => 'Country life',
        '304' => 'Cityscapes',
        '305' => 'Industrial',
        '306' => 'Bridges',
        '307' => 'Abandoned buildings',
        '308' => 'Parks, gardens',
        '309' => 'Transportation',
        '310' => 'City panorama',
        '400' => 'Architecture',
        '401' => 'Exterior',
        '402' => 'Interior',
        '403' => 'Statues, monuments',
        '404' => 'Churches',
        '500' => 'Still life',
        '501' => 'Plants, flowers',
        '502' => 'Food, drinks',
        '503' => 'Dolls, figures',
        '504' => 'Other objects',
        '600' => 'Macro photography',
        '601' => 'Objects',
        '602' => 'Nature',
        '603' => 'Human',
        '700' => 'Photojournalism',
        '701' => 'People',
        '703' => 'Perfoming, show',
        '704' => 'Public gatherings, events',
        '705' => 'Sport',
        '706' => 'Political',
        '707' => 'Military',
        '708' => 'Documentary, editorial',
        '709' => 'Journalism miscellaneous',
        '800' => 'Commercial photography',
        '801' => 'Products',
        '802' => 'Services',
        '803' => 'Fashion',
        '804' => 'Commercial miscellaneous',
        '900' => 'Special categories',
        '901' => 'Conceptual photography',
        '902' => 'Horror, macabre',
        '903' => 'Abstract photography',
        '904' => 'Surreal photography',
        '905' => 'Trash',
        '906' => 'Everything and anything',
        '907' => '3D-photography',
        '1000' => 'Digital art',
        '1001' => '3D-Design',
        '1002' => 'Work art',
        '1003' => 'Work collage',
        '1004' => 'Web-Design',
        '1005' => 'Logo',
        '1006' => 'Poster',
        '1007' => 'Cover',
        '1008' => 'Graphic art',
        '1009' => 'Anime, manga',
        '1010' => 'Postcards',
        '1100' => 'Wedding photography',
        '1101' => 'Wedding reportage',
        '1102' => 'Wedding staging',
        '1103' => 'Wedding portraits',
        '1200' => 'Fine-art photography',
        '1201' => 'Fine-art photography',
        '2200' => 'Out of Rating',
        '2201' => 'Work humor',
        '2202' => 'Work riddle',
        '2203' => 'Community faces',
        '2204' => 'Retro photography',
        '2205' => 'Family album',
        '2300' => 'Portfolio',
    );

    private static $loc_de = array(
        'main_title_loc' => 'Fotocommunity',
        'logout_loc' => 'Abmelden',
        'login_short_loc' => 'E-Mail',
        'pass_loc' => 'Passwort',
        'enter_short_loc' => 'Anmelden',
        'profile_title_loc' => 'Mein Profil',
        'comm_loc' => 'Anmerkungen',
        'comm_del_by_admin_loc' => 'Die Anmerkung wurde von Administrator gelöscht',
        'comm_del_by_author_loc' => 'Die Anmerkung wurde gelöscht',
        'recomm_works_loc' => 'Empfohlene Bilder',
        'all_works_loc' => 'Alle Bilder',
        'special_works_loc' => 'Besondere Bilder',
        'popular_loc' => 'Beliebte Bilder',
        'critique_works_loc' => 'Bilder mit Kritikanfrage',
        'works_of_day_loc' => 'Bilder des Tages',
        'friends_works_loc' => 'Bilder meiner Freunde',
        'fav_auth_works_loc' => 'Ich folge',
        'auth_works_you_follow_loc' => 'Bilder von mir gefolgten Benutzern',
        'works_loc' => 'Bilder',
        'rating_loc' => 'Rating',
        'recs_loc' => 'Empfehlungen',
        'add_rec_loc' => 'Empfehlen',
        'already_rec_note_loc' => 'Empfehlung<br />wurde gezählt',
        'add_comm_loc' => 'Anmerkung hinzufügen',
        'wrong_login_pass_short_loc' => 'Falscher Login bzw. Passwort',
        'site_full_ver_loc' => 'Desktop-Version',
        'anonymous_loc' => 'Anonym',
        'author_loc' => 'Autor',
        'limit_recs_achieved_1_loc' => 'Sie können',
        'limit_recs_achieved_2_loc' => 'Bilder per Tag empfehlen',
        'add_favorite_loc' => 'Folgen',
        'del_favorite_loc' => 'Entfolgen',
        'forbidden_write_comm_loc' => 'Die Anmerkung Option zu diesem Bild deaktiviert',
        'portfolio_loc' => 'Portfolio',
        'without_name' => 'Ohne Titel',
        'success_unsubscribe' => 'Sie haben den Newsletter abbestellt',
        'success_unsubscribe_top' => 'Sie haben den Newsletter<br> mit besten Bildern und interessanten Anmerkungen abbestellt',
        'author_blocked_loc' => 'Dieser Benutzer oder dieses Bild ist vorübergehend gesperrt',
        'access_nu_cat_loc' => 'Zugriff auf den Aktbereich',
        'add_to_fineart' => 'Künst.Foto',
        'remove_from_fineart' => 'Kein künst.Foto',

        'smile_loc' => 'Grinsen',
        'wink_loc' => 'Zwinkern',
        'photo_smile_loc' => 'Foto',
        'super_loc' => 'Super',
        'beer_loc' => 'Bier',
        'eek_loc' => 'Überrascht',
        'evil_loc' => 'Teuflisch',
        'curve_smile_loc' => 'Fragend',
        'love_loc' => 'Verliebt',
        'naezd_loc' => 'Auseinandersetzung',
        'lol_loc' => 'Breites Grinsen',
        'rolleyes_loc' => 'Denken',
        'insane_loc' => 'Ähmmm...',
        'znaika_loc' => 'Expert',
        'nopets_loc' => 'No pets!',
        'molotok_loc' => 'Klasse',
        'lamer_loc' => 'Anfänger',
        'redtongue_loc' => 'Grinsen mit Zunge',
        'idea_loc' => 'Ich habe eine Idee',
        'confused_loc' => 'Unsicher',
        'dontlike_loc' => 'Gefällt mir nicht',
        'moderator_loc' => 'Widerspreche',
        'shuffle_loc' => 'Verlegen',
        'frown_loc' => 'Traurig',
        'weep_loc' => 'Weinen',
        'roof_loc' => 'Das ist doch Wahnsinn',
        'hair_loc' => 'Ich kann es kaum fassen',
        'puke_loc' => 'Übergeben',
        'buzz_loc' => 'Zerstöre deine Kamera',
        'taz_loc' => 'Vom Thema abweichend',
    );

    private static $cat_names_de = array(
        '100' => 'Menschen und Portraits',
        '101' => 'Glamour Portrait',
        '102' => 'Klassisches Portrait',
        '103' => 'Spontanes Portrait',
        '104' => 'Expressives Portrait',
        '105' => 'Emotionales Portrait',
        '106' => 'Selbstportrait',
        '107' => 'Kinder, Babys',
        '108' => 'Fashion Portrait',
        '109' => 'Fetisch Portrait',
        '110' => 'Pin-up',
        '111' => 'Akt',
        '200' => 'Natur und Tiere',
        '201' => 'Landschaften',
        '202' => 'Blumen, Pflanzen',
        '203' => 'Naturphänomene',
        '204' => 'Haustiere',
        '205' => 'Wirbellose',
        '206' => 'Reptilien',
        '207' => 'Vögeln',
        '208' => 'Unterwasserwelt',
        '209' => 'Tierwelt',
        '210' => 'Geologie',
        '211' => 'Landschaftspanorama',
        '300' => 'Stadt und Land',
        '301' => 'Straßenfotografie',
        '302' => 'Stadtleben',
        '303' => 'Landleben',
        '304' => 'Stadtansichten',
        '305' => 'Industriefotografie',
        '306' => 'Brücken',
        '307' => 'Verlassene Gebäude',
        '308' => 'Parks und Gärten',
        '309' => 'Verkehr, Fahrzeuge',
        '310' => 'Stadtpanorama',
        '400' => 'Architektur',
        '401' => 'Außenaufnahmen',
        '402' => 'Innenaufnahmen',
        '403' => 'Denkmäler',
        '404' => 'Tempeln, Kirchen',
        '500' => 'Stillleben',
        '501' => 'Pflanzen, Blumen',
        '502' => 'Lebensmittel, Getränke',
        '503' => 'Puppen, Figurinen',
        '504' => 'Sonstige Gegenstände',
        '600' => 'Makrofotografie',
        '601' => 'Gegenstände',
        '602' => 'Natur',
        '603' => 'Mensch',
        '700' => 'Reportage',
        '701' => 'Menschen',
        '703' => 'Auftritte, Shows',
        '704' => 'Tagungen, Veranstaltungen',
        '705' => 'Sport',
        '706' => 'Politik',
        '707' => 'Militär',
        '708' => 'Dokumentarfotografie',
        '709' => 'Sonstige Reportagen',
        '800' => 'Kommerzielle Fotografie',
        '801' => 'Produkten',
        '802' => 'Dienstleistungen',
        '803' => 'Fashion',
        '804' => 'Sonstige kommerzielle Foto',
        '900' => 'Sonstige Kategorien',
        '901' => 'Konzept-Fotografie',
        '902' => 'Horror',
        '903' => 'Abstraktionismus',
        '904' => 'Surrealismus',
        '905' => 'Trash',
        '906' => 'Alltagsfotografie',
        '907' => '3D-Foto',
        '1000' => 'Digitale Kunst',
        '1001' => '3D-Design',
        '1002' => 'Fotoart',
        '1003' => 'Kollage',
        '1004' => 'Web-Design',
        '1005' => 'Logo',
        '1006' => 'Plakat',
        '1007' => 'Cover',
        '1008' => 'Grafik',
        '1009' => 'Anime',
        '1010' => 'Grußkarte',
        '1100' => 'Hochzeitsfotografie',
        '1101' => 'Hochzeitsreportage',
        '1102' => 'Hochzeit Inszenierung',
        '1103' => 'Hochzeitsportrait',
        '1200' => 'Künstlerische Fotografie',
        '1201' => 'Künstlerische Fotografie',
        '2200' => 'Fotos onhe Rating',
        '2201' => 'Fotoscherze',
        '2202' => 'Fotorätsel',
        '2203' => 'Community Gesichter',
        '2204' => 'Retro-Fotografie',
        '2205' => 'Familienalbum',
        '2300' => 'Portfolio',
    );

    public static $loc_by = array();
    public static $cat_names_by = array();

    public static function inst()
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new Localizer();
        }
        return $instance;
    }

    /**
     * Private __construct so nobody else can instance it
     */
    private function __construct()
    {
        Localizer::init();
    }

    public static function init()
    {
        if (Config::$lang == 'by') {
            Localizer::$loc = Localizer::$loc_by;
            Localizer::$cat_names = Localizer::$cat_names_by;

            Localizer::$col_port_index_body = 'port_index_body';
            Localizer::$col_port_menu_group_name = 'port_menu_group_name';
            Localizer::$col_port_title = 'port_title';
            Localizer::$col_port_subtitle = 'port_subtitle';
            Localizer::$col_port_seo_title = 'port_seo_title';
            Localizer::$col_port_seo_desc = 'port_seo_desc';
            Localizer::$col_port_seo_keys = 'port_seo_keys';

            Localizer::$tbl_ds_comments = 'ds_comments';
            Localizer::$col_auth_name = 'auth_name';
            Localizer::$col_auth_mood = 'auth_mood';
            Localizer::$col_ph_name = 'ph_name';
            Localizer::$col_serie_name = 'serie_name';
            Localizer::$col_album_name = 'album_name';
            Localizer::$col_comm_text = 'comm_text';
            Localizer::$col_ph_comm_cnt = 'ph_comm_cnt';
            Localizer::$col_comp_name = 'comp_name';
        } else if (Config::$lang == 'de') {
            Localizer::$loc = Localizer::$loc_de;
            Localizer::$cat_names = Localizer::$cat_names_de;

            Localizer::$col_port_index_body = 'port_index_body';
            Localizer::$col_port_menu_group_name = 'port_menu_group_name';
            Localizer::$col_port_title = 'port_title';
            Localizer::$col_port_subtitle = 'port_subtitle';
            Localizer::$col_port_seo_title = 'port_seo_title';
            Localizer::$col_port_seo_desc = 'port_seo_desc';
            Localizer::$col_port_seo_keys = 'port_seo_keys';

            Localizer::$tbl_ds_comments = 'ds_comments_de';
            Localizer::$col_auth_name = 'auth_name_com';
            Localizer::$col_auth_mood = 'auth_mood_com';
            Localizer::$col_ph_name = 'ph_name_de';
            Localizer::$col_serie_name = 'serie_name_de';
            Localizer::$col_album_name = 'album_name_de';
            Localizer::$col_comm_text = 'comm_text_de';
            Localizer::$col_ph_comm_cnt = 'ph_comm_cnt_de';
            Localizer::$col_comp_name = 'comp_name_de';
        } else if (Config::$lang == 'com') {
            Localizer::$loc = Localizer::$loc_com;
            Localizer::$cat_names = Localizer::$cat_names_com;

            Localizer::$col_port_index_body = 'port_index_body_com';
            Localizer::$col_port_menu_group_name = 'port_menu_group_name_com';
            Localizer::$col_port_title = 'port_title_com';
            Localizer::$col_port_subtitle = 'port_subtitle_com';
            Localizer::$col_port_seo_title = 'port_seo_title_com';
            Localizer::$col_port_seo_desc = 'port_seo_desc_com';
            Localizer::$col_port_seo_keys = 'port_seo_keys_com';

            Localizer::$tbl_ds_comments = 'ds_comments_com';
            Localizer::$col_auth_name = 'auth_name_com';
            Localizer::$col_auth_mood = 'auth_mood_com';
            Localizer::$col_ph_name = 'ph_name_com';
            Localizer::$col_serie_name = 'serie_name_com';
            Localizer::$col_album_name = 'album_name_com';
            Localizer::$col_comm_text = 'comm_text_com';
            Localizer::$col_ph_comm_cnt = 'ph_comm_cnt_com';
            Localizer::$col_comp_name = 'comp_name_com';
        } else {
            Localizer::$loc = Localizer::$loc_ru;
            Localizer::$cat_names = Localizer::$cat_names_ru;

            Localizer::$col_port_index_body = 'port_index_body';
            Localizer::$col_port_menu_group_name = 'port_menu_group_name';
            Localizer::$col_port_title = 'port_title';
            Localizer::$col_port_subtitle = 'port_subtitle';
            Localizer::$col_port_seo_title = 'port_seo_title';
            Localizer::$col_port_seo_desc = 'port_seo_desc';
            Localizer::$col_port_seo_keys = 'port_seo_keys';

            Localizer::$tbl_ds_comments = 'ds_comments';
            Localizer::$col_auth_name = 'auth_name';
            Localizer::$col_auth_mood = 'auth_mood';
            Localizer::$col_ph_name = 'ph_name';
            Localizer::$col_serie_name = 'serie_name';
            Localizer::$col_album_name = 'album_name';
            Localizer::$col_comm_text = 'comm_text';
            Localizer::$col_ph_comm_cnt = 'ph_comm_cnt';
            Localizer::$col_comp_name = 'comp_name';
        }
    }

    private static $loc_ru = array(
        'main_title_loc' => 'Фотосайт / Сообщество фотографов',
        'logout_loc' => 'Выйти',
        'login_short_loc' => 'E-mail',
        'pass_loc' => 'Пароль',
        'enter_short_loc' => 'Войти',
        'profile_title_loc' => 'Мой профиль',
        'comm_loc' => 'Сообщения',
        'comm_del_by_admin_loc' => 'Сообщение было удалено администрацией',
        'comm_del_by_author_loc' => 'Сообщение было удалено автором',
        'recomm_works_loc' => 'Рекомендованные работы',
        'all_works_loc' => 'Все работы',
        'special_works_loc' => 'Особый взгляд',
        'popular_loc' => 'Популярные работы',
        'critique_works_loc' => 'Работы для критики',
        'works_of_day_loc' => 'Работы дня',
        'friends_works_loc' => 'Работы друзей',
        'fav_auth_works_loc' => 'Я подписан',
        'auth_works_you_follow_loc' => 'Работы авторов на которых я подписан',
        'works_loc' => 'Работы',
        'rating_loc' => 'Рейтинг',
        'recs_loc' => 'Рекомендации',
        'add_rec_loc' => 'Рекомендовать',
        'already_rec_note_loc' => 'Рекомендация<br />принята',
        'add_comm_loc' => 'Добавить сообщение',
        'wrong_login_pass_short_loc' => 'Неверный логин или пароль',
        'site_full_ver_loc' => 'Полная версия сайта',
        'anonymous_loc' => 'Анонимно',
        'author_loc' => 'Автор',
        'limit_recs_achieved_1_loc' => 'В день можно оставить',
        'limit_recs_achieved_2_loc' => 'рекомендаций',
        'add_favorite_loc' => 'Подписаться',
        'del_favorite_loc' => 'Отписаться',
        'forbidden_write_comm_loc' => 'Возможность написания сообщений к этой работе отключена',
        'portfolio_loc' => 'Портфолио',
        'without_name' => 'Без названия',
        'success_unsubscribe' => 'Вы успешно отписались от рассылки',
        'success_unsubscribe_top' => 'Вы успешно отписались<br>от рассылки о самых интересных событиях на сайте',
        'author_blocked_loc' => 'Автор или данная работа временно заблокированы',
        'access_nu_cat_loc' => 'Доступ к категории Ню',
        'add_to_fineart' => 'Худ.фото',
        'remove_from_fineart' => 'Не худ.фото',

        'smile_loc' => 'Улыбка',
        'wink_loc' => 'Подмигивание',
        'photo_smile_loc' => 'Фото',
        'super_loc' => 'Супер',
        'beer_loc' => 'Пиво',
        'eek_loc' => 'Шок',
        'evil_loc' => 'Дьявол',
        'curve_smile_loc' => 'Ухмылка',
        'love_loc' => 'Любовь',
        'naezd_loc' => 'На повышенных тонах',
        'lol_loc' => 'Смех',
        'rolleyes_loc' => 'Задумчивый',
        'insane_loc' => 'Непонимание',
        'znaika_loc' => 'Знаю, что говорю',
        'nopets_loc' => 'Не надо животных',
        'molotok_loc' => 'Молоток',
        'lamer_loc' => 'Чайник',
        'redtongue_loc' => 'Язык',
        'idea_loc' => 'Идея',
        'confused_loc' => 'Неуверенность',
        'dontlike_loc' => 'Не нравится',
        'moderator_loc' => 'Несогласен',
        'shuffle_loc' => 'Смущенный',
        'frown_loc' => 'Грустный',
        'weep_loc' => 'Я рыдаю',
        'roof_loc' => 'Крыша едет',
        'hair_loc' => 'Волосы дыбом',
        'puke_loc' => 'Противно',
        'buzz_loc' => 'Разбей свой тапарат',
        'taz_loc' => 'Греметь тазиком',

    );
}

Localizer::inst();