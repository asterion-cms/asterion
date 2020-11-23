<?php
/**
 * @class Language
 *
 * This class represents a language for the website.
 * It is used to manage the different translations.
 *
 * @author Leano Martinet <info@asterion-cms.com>
 * @package Asterion
 * @version 4.0.0
 */
class Language extends Db_Object
{

    /**
     * Initialize the translations.
     */
    public static function init()
    {
        $languages = Language::languages();
        $languageUrl = (isset($_GET['language'])) ? $_GET['language'] : '';
        $language = (isset($languages[$languageUrl])) ? $languages[$languageUrl] : reset($languages);
        Session::set('language', $language);
        if (ASTERION_DB_USE == true) {
            $_ENV['translations'] = Translation::load($language['id']);
        }
    }

    /**
     * Get the languages.
     */
    public static function languages()
    {
        if (!isset($_ENV['languages'])) {
            Language::load();
        }
        return $_ENV['languages'];
    }

    /**
     * Fill the laguange code and labels into ENV variables.
     */
    public static function load()
    {
        if (ASTERION_DB_USE == true) {
            $query = 'SELECT * FROM ' . Db::prefixTable('language') . ' ORDER BY ord';
            $languages = [];
            foreach (Db::returnAll($query, [], false) as $item) {
                $languages[$item['id']] = $item;
            }
            $_ENV['languages'] = $languages;
        } else {
            $_ENV['languages'] = [['id' => constant('ASTERION_LANGUAGE_ID')]];
        }
    }

    /**
     * Get the active language
     */
    public static function active()
    {
        return (isset(Session::get('language')['id'])) ? Session::get('language')['id'] : '';
    }

    /**
     * Format the table field using the languages.
     */
    public static function field($field)
    {
        $result = '';
        foreach (Language::languages() as $language) {
            $result .= $field . '_' . $language . ',';
        }
        return substr($result, 0, -1);
    }

    /**
     * Overwrite the insert function and check all the objects.
     */
    public function insert($values, $options = [])
    {
        parent::insert($values, $options);
        $this->updateObjects('insert', $values);
        Init::saveTranslation($this->id());
    }

    /**
     * Overwrite the modify function and check all the objects.
     */
    public function modify($values, $options = [])
    {
        $values['oldId'] = Text::simpleUrl($values['id_oldId'], '');
        $values['newId'] = Text::simpleUrl($values['id'], '');
        parent::modify($values, $options);
        if ($this->id() != '' && $values['oldId'] != '' && $values['newId'] != '' && $values['oldId'] != $values['newId']) {
            $this->updateObjects('modify', $values);
        }
    }

    /**
     * Overwrite the delete function and check all the objects.
     */
    public function delete()
    {
        parent::delete();
        if ($this->id() != '') {
            $this->updateObjects('delete', []);
        }
    }

    /**
     * Modify all the objects that have translatable attributes.
     */
    public function updateObjects($mode, $values)
    {
        $objectNames = File::scanDirectoryObjects();
        foreach ($objectNames as $objectName) {
            $object = new $objectName();
            $tableName = $object->info->table;
            $attributes = $object->getAttributes();
            foreach ($attributes as $attribute) {
                $attributeName = (string) $attribute->name;
                if ((string) $attribute->language == 'true') {
                    switch ($mode) {
                        case 'insert':
                            $query = 'ALTER TABLE ' . Db::prefixTable($tableName) . '
                                        ADD ' . $attributeName . '_' . $values['id'] . '
                                        VARCHAR(255) COLLATE utf8_unicode_ci';
                            Db::execute($query);
                            break;
                        case 'modify':
                            $query = 'ALTER TABLE ' . Db::prefixTable($tableName) . '
                                        CHANGE ' . $attributeName . '_' . $values['oldId'] . ' ' . $attributeName . '_' . $values['newId'] . '
                                        VARCHAR(255) COLLATE utf8_unicode_ci';
                            Db::execute($query);
                            break;
                        case 'delete':
                            $query = 'ALTER TABLE ' . Db::prefixTable($tableName) . '
                                        DROP ' . $attributeName . '_' . $this->id();
                            Db::execute($query);
                            break;
                    }
                }
            }
        }
    }

    /**
     * List of all ISO languages
     */
    public static function isoList()
    {
        return [
            'ab' => ['group' => 'Northwest Caucasian', 'name' => 'Abkhazian', 'local_names' => 'аҧсуа бызшәа, аҧсшәа'],
            'aa' => ['group' => 'Afro-Asiatic', 'name' => 'Afar', 'local_names' => 'Afaraf'],
            'af' => ['group' => 'Indo-European', 'name' => 'Afrikaans', 'local_names' => 'Afrikaans'],
            'ak' => ['group' => 'Niger–Congo', 'name' => 'Akan', 'local_names' => 'Akan'],
            'sq' => ['group' => 'Indo-European', 'name' => 'Albanian', 'local_names' => 'Shqip'],
            'am' => ['group' => 'Afro-Asiatic', 'name' => 'Amharic', 'local_names' => 'አማርኛ'],
            'ar' => ['group' => 'Afro-Asiatic', 'name' => 'Arabic', 'local_names' => 'العربية'],
            'an' => ['group' => 'Indo-European', 'name' => 'Aragonese', 'local_names' => 'aragonés'],
            'hy' => ['group' => 'Indo-European', 'name' => 'Armenian', 'local_names' => 'Հայերեն'],
            'as' => ['group' => 'Indo-European', 'name' => 'Assamese', 'local_names' => 'অসমীয়া'],
            'av' => ['group' => 'Northeast Caucasian', 'name' => 'Avaric', 'local_names' => 'авар мацӀ, магӀарул мацӀ'],
            'ae' => ['group' => 'Indo-European', 'name' => 'Avestan', 'local_names' => 'avesta'],
            'ay' => ['group' => 'Aymaran', 'name' => 'Aymara', 'local_names' => 'aymar aru'],
            'az' => ['group' => 'Turkic', 'name' => 'Azerbaijani', 'local_names' => 'azərbaycan dili'],
            'bm' => ['group' => 'Niger–Congo', 'name' => 'Bambara', 'local_names' => 'bamanankan'],
            'ba' => ['group' => 'Turkic', 'name' => 'Bashkir', 'local_names' => 'башҡорт теле'],
            'eu' => ['group' => 'Languageuage isolate', 'name' => 'Basque', 'local_names' => 'euskara, euskera'],
            'be' => ['group' => 'Indo-European', 'name' => 'Belarusian', 'local_names' => 'беларуская мова'],
            'bn' => ['group' => 'Indo-European', 'name' => 'Bengali', 'local_names' => 'বাংলা'],
            'bh' => ['group' => 'Indo-European', 'name' => 'Bihari languages', 'local_names' => 'भोजपुरी'],
            'bi' => ['group' => 'Creole', 'name' => 'Bislama', 'local_names' => 'Bislama'],
            'bs' => ['group' => 'Indo-European', 'name' => 'Bosnian', 'local_names' => 'bosanski jezik'],
            'br' => ['group' => 'Indo-European', 'name' => 'Breton', 'local_names' => 'brezhoneg'],
            'bg' => ['group' => 'Indo-European', 'name' => 'Bulgarian', 'local_names' => 'български език'],
            'my' => ['group' => 'Sino-Tibetan', 'name' => 'Burmese', 'local_names' => 'ဗမာစာ'],
            'ca' => ['group' => 'Indo-European', 'name' => 'Catalan, Valencian', 'local_names' => 'català, valencià'],
            'ch' => ['group' => 'Austronesian', 'name' => 'Chamorro', 'local_names' => 'Chamoru'],
            'ce' => ['group' => 'Northeast Caucasian', 'name' => 'Chechen', 'local_names' => 'нохчийн мотт'],
            'ny' => ['group' => 'Niger–Congo', 'name' => 'Chichewa, Chewa, Nyanja', 'local_names' => 'chiCheŵa, chinyanja'],
            'zh' => ['group' => 'Sino-Tibetan', 'name' => 'Chinese', 'local_names' => '中文 (Zhōngwén), 汉语, 漢語'],
            'cv' => ['group' => 'Turkic', 'name' => 'Chuvash', 'local_names' => 'чӑваш чӗлхи'],
            'kw' => ['group' => 'Indo-European', 'name' => 'Cornish', 'local_names' => 'Kernewek'],
            'co' => ['group' => 'Indo-European', 'name' => 'Corsican', 'local_names' => 'corsu, lingua corsa'],
            'cr' => ['group' => 'Algonquian', 'name' => 'Cree', 'local_names' => 'ᓀᐦᐃᔭᐍᐏᐣ'],
            'hr' => ['group' => 'Indo-European', 'name' => 'Croatian', 'local_names' => 'hrvatski jezik'],
            'cs' => ['group' => 'Indo-European', 'name' => 'Czech', 'local_names' => 'čeština, český jazyk'],
            'da' => ['group' => 'Indo-European', 'name' => 'Danish', 'local_names' => 'dansk'],
            'dv' => ['group' => 'Indo-European', 'name' => 'Divehi, Dhivehi, Maldivian', 'local_names' => 'ދިވެހި'],
            'nl' => ['group' => 'Indo-European', 'name' => 'Dutch, Flemish', 'local_names' => 'Nederlands, Vlaams'],
            'dz' => ['group' => 'Sino-Tibetan', 'name' => 'Dzongkha', 'local_names' => 'རྫོང་ཁ'],
            'en' => ['group' => 'Indo-European', 'name' => 'English', 'local_names' => 'English'],
            'eo' => ['group' => 'Constructed', 'name' => 'Esperanto', 'local_names' => 'Esperanto'],
            'et' => ['group' => 'Uralic', 'name' => 'Estonian', 'local_names' => 'eesti, eesti keel'],
            'ee' => ['group' => 'Niger–Congo', 'name' => 'Ewe', 'local_names' => 'Eʋegbe'],
            'fo' => ['group' => 'Indo-European', 'name' => 'Faroese', 'local_names' => 'føroyskt'],
            'fj' => ['group' => 'Austronesian', 'name' => 'Fijian', 'local_names' => 'vosa Vakaviti'],
            'fi' => ['group' => 'Uralic', 'name' => 'Finnish', 'local_names' => 'suomi, suomen kieli'],
            'fr' => ['group' => 'Indo-European', 'name' => 'French', 'local_names' => 'français, langue française'],
            'ff' => ['group' => 'Niger–Congo', 'name' => 'Fulah', 'local_names' => 'Fulfulde, Pulaar, Pular'],
            'gl' => ['group' => 'Indo-European', 'name' => 'Galician', 'local_names' => 'Galego'],
            'ka' => ['group' => 'Kartvelian', 'name' => 'Georgian', 'local_names' => 'ქართული'],
            'de' => ['group' => 'Indo-European', 'name' => 'German', 'local_names' => 'Deutsch'],
            'el' => ['group' => 'Indo-European', 'name' => 'Greek, Modern (1453–)', 'local_names' => 'ελληνικά'],
            'gn' => ['group' => 'Tupian', 'name' => 'Guarani', 'local_names' => 'Avañe\'ẽ'],
            'gu' => ['group' => 'Indo-European', 'name' => 'Gujarati', 'local_names' => 'ગુજરાતી'],
            'ht' => ['group' => 'Creole', 'name' => 'Haitian, Haitian Creole', 'local_names' => 'Kreyòl ayisyen'],
            'ha' => ['group' => 'Afro-Asiatic', 'name' => 'Hausa', 'local_names' => '(Hausa) هَوُسَ'],
            'he' => ['group' => 'Afro-Asiatic', 'name' => 'Hebrew', 'local_names' => 'עברית'],
            'hz' => ['group' => 'Niger–Congo', 'name' => 'Herero', 'local_names' => 'Otjiherero'],
            'hi' => ['group' => 'Indo-European', 'name' => 'Hindi', 'local_names' => 'हिन्दी, हिंदी'],
            'ho' => ['group' => 'Austronesian', 'name' => 'Hiri Motu', 'local_names' => 'Hiri Motu'],
            'hu' => ['group' => 'Uralic', 'name' => 'Hungarian', 'local_names' => 'magyar'],
            'ia' => ['group' => 'Constructed', 'name' => 'Interlingua', 'local_names' => 'Interlingua'],
            'id' => ['group' => 'Austronesian', 'name' => 'Indonesian', 'local_names' => 'Bahasa Indonesia'],
            'ie' => ['group' => 'Constructed', 'name' => 'Interlingue, Occidental', 'local_names' => ' Occidental, Interlingue'],
            'ga' => ['group' => 'Indo-European', 'name' => 'Irish', 'local_names' => 'Gaeilge'],
            'ig' => ['group' => 'Niger–Congo', 'name' => 'Igbo', 'local_names' => 'Asụsụ Igbo'],
            'ik' => ['group' => 'Eskimo–Aleut', 'name' => 'Inupiaq', 'local_names' => 'Iñupiaq, Iñupiatun'],
            'io' => ['group' => 'Constructed', 'name' => 'Ido', 'local_names' => 'Ido'],
            'is' => ['group' => 'Indo-European', 'name' => 'Icelandic', 'local_names' => 'Íslenska'],
            'it' => ['group' => 'Indo-European', 'name' => 'Italian', 'local_names' => 'Italiano'],
            'iu' => ['group' => 'Eskimo–Aleut', 'name' => 'Inuktitut', 'local_names' => 'ᐃᓄᒃᑎᑐᑦ'],
            'ja' => ['group' => 'Japonic', 'name' => 'Japanese', 'local_names' => '日本語 (にほんご)'],
            'jv' => ['group' => 'Austronesian', 'name' => 'Javanese', 'local_names' => 'ꦧꦱꦗꦮ, Basa Jawa'],
            'kl' => ['group' => 'Eskimo–Aleut', 'name' => 'Kalaallisut, Greenlandic', 'local_names' => 'kalaallisut, kalaallit oqaasii'],
            'kn' => ['group' => 'Dravidian', 'name' => 'Kannada', 'local_names' => 'ಕನ್ನಡ'],
            'kr' => ['group' => 'Nilo-Saharan', 'name' => 'Kanuri', 'local_names' => 'Kanuri'],
            'ks' => ['group' => 'Indo-European', 'name' => 'Kashmiri', 'local_names' => 'कश्मीरी, كشميري‎'],
            'kk' => ['group' => 'Turkic', 'name' => 'Kazakh', 'local_names' => 'қазақ тілі'],
            'km' => ['group' => 'Austroasiatic', 'name' => 'Central Khmer', 'local_names' => 'ខ្មែរ, ខេមរភាសា, ភាសាខ្មែរ'],
            'ki' => ['group' => 'Niger–Congo', 'name' => 'Kikuyu, Gikuyu', 'local_names' => 'Gĩkũyũ'],
            'rw' => ['group' => 'Niger–Congo', 'name' => 'Kinyarwanda', 'local_names' => 'Ikinyarwanda'],
            'ky' => ['group' => 'Turkic', 'name' => 'Kirghiz, Kyrgyz', 'local_names' => 'Кыргызча, Кыргыз тили'],
            'kv' => ['group' => 'Uralic', 'name' => 'Komi', 'local_names' => 'коми кыв'],
            'kg' => ['group' => 'Niger–Congo', 'name' => 'Kongo', 'local_names' => 'Kikongo'],
            'ko' => ['group' => 'Koreanic', 'name' => 'Korean', 'local_names' => '한국어'],
            'ku' => ['group' => 'Indo-European', 'name' => 'Kurdish', 'local_names' => 'Kurdî, کوردی‎'],
            'kj' => ['group' => 'Niger–Congo', 'name' => 'Kuanyama, Kwanyama', 'local_names' => 'Kuanyama'],
            'la' => ['group' => 'Indo-European', 'name' => 'Latin', 'local_names' => 'latine, lingua latina'],
            'lb' => ['group' => 'Indo-European', 'name' => 'Luxembourgish, Letzeburgesch', 'local_names' => 'Lëtzebuergesch'],
            'lg' => ['group' => 'Niger–Congo', 'name' => 'Ganda', 'local_names' => 'Luganda'],
            'li' => ['group' => 'Indo-European', 'name' => 'Limburgan, Limburger, Limburgish', 'local_names' => 'Limburgs'],
            'ln' => ['group' => 'Niger–Congo', 'name' => 'Lingala', 'local_names' => 'Lingála'],
            'lo' => ['group' => 'Tai–Kadai', 'name' => 'Lao', 'local_names' => 'ພາສາລາວ'],
            'lt' => ['group' => 'Indo-European', 'name' => 'Lithuanian', 'local_names' => 'lietuvių kalba'],
            'lu' => ['group' => 'Niger–Congo', 'name' => 'Luba-Katanga', 'local_names' => 'Kiluba'],
            'lv' => ['group' => 'Indo-European', 'name' => 'Latvian', 'local_names' => 'latviešu valoda'],
            'gv' => ['group' => 'Indo-European', 'name' => 'Manx', 'local_names' => 'Gaelg, Gailck'],
            'mk' => ['group' => 'Indo-European', 'name' => 'Macedonian', 'local_names' => 'македонски јазик'],
            'mg' => ['group' => 'Austronesian', 'name' => 'Malagasy', 'local_names' => 'fiteny malagasy'],
            'ms' => ['group' => 'Austronesian', 'name' => 'Malay', 'local_names' => 'Bahasa Melayu, بهاس ملايو‎'],
            'ml' => ['group' => 'Dravidian', 'name' => 'Malayalam', 'local_names' => 'മലയാളം'],
            'mt' => ['group' => 'Afro-Asiatic', 'name' => 'Maltese', 'local_names' => 'Malti'],
            'mi' => ['group' => 'Austronesian', 'name' => 'Maori', 'local_names' => 'te reo Māori'],
            'mr' => ['group' => 'Indo-European', 'name' => 'Marathi', 'local_names' => 'मराठी'],
            'mh' => ['group' => 'Austronesian', 'name' => 'Marshallese', 'local_names' => 'Kajin M̧ajeļ'],
            'mn' => ['group' => 'Mongolic', 'name' => 'Mongolian', 'local_names' => 'Монгол хэл'],
            'na' => ['group' => 'Austronesian', 'name' => 'Nauru', 'local_names' => 'Dorerin Naoero'],
            'nv' => ['group' => 'Dené–Yeniseian', 'name' => 'Navajo, Navaho', 'local_names' => 'Diné bizaad'],
            'nd' => ['group' => 'Niger–Congo', 'name' => 'North Ndebele', 'local_names' => 'isiNdebele'],
            'ne' => ['group' => 'Indo-European', 'name' => 'Nepali', 'local_names' => 'नेपाली'],
            'ng' => ['group' => 'Niger–Congo', 'name' => 'Ndonga', 'local_names' => 'Owambo'],
            'nb' => ['group' => 'Indo-European', 'name' => 'Norwegian Bokmål', 'local_names' => 'Norsk Bokmål'],
            'nn' => ['group' => 'Indo-European', 'name' => 'Norwegian Nynorsk', 'local_names' => 'Norsk Nynorsk'],
            'no' => ['group' => 'Indo-European', 'name' => 'Norwegian', 'local_names' => 'Norsk'],
            'ii' => ['group' => 'Sino-Tibetan', 'name' => 'Sichuan Yi, Nuosu', 'local_names' => 'ꆈꌠ꒿ Nuosuhxop'],
            'nr' => ['group' => 'Niger–Congo', 'name' => 'South Ndebele', 'local_names' => 'isiNdebele'],
            'oc' => ['group' => 'Indo-European', 'name' => 'Occitan', 'local_names' => 'occitan, lenga d\'òc'],
            'oj' => ['group' => 'Algonquian', 'name' => 'Ojibwa', 'local_names' => 'ᐊᓂᔑᓈᐯᒧᐎᓐ'],
            'cu' => ['group' => 'Indo-European', 'name' => 'Church Slavic, Old Bulgarian', 'local_names' => 'ѩзыкъ словѣньскъ'],
            'om' => ['group' => 'Afro-Asiatic', 'name' => 'Oromo', 'local_names' => 'Afaan Oromoo'],
            'or' => ['group' => 'Indo-European', 'name' => 'Oriya', 'local_names' => 'ଓଡ଼ିଆ'],
            'os' => ['group' => 'Indo-European', 'name' => 'Ossetian, Ossetic', 'local_names' => 'ирон æвзаг'],
            'pa' => ['group' => 'Indo-European', 'name' => 'Punjabi, Panjabi', 'local_names' => 'ਪੰਜਾਬੀ, پنجابی‎'],
            'pi' => ['group' => 'Indo-European', 'name' => 'Pali', 'local_names' => 'पालि, पाळि'],
            'fa' => ['group' => 'Indo-European', 'name' => 'Persian', 'local_names' => 'فارسی'],
            'pl' => ['group' => 'Indo-European', 'name' => 'Polish', 'local_names' => 'język polski, polszczyzna'],
            'ps' => ['group' => 'Indo-European', 'name' => 'Pashto, Pushto', 'local_names' => 'پښتو'],
            'pt' => ['group' => 'Indo-European', 'name' => 'Portuguese', 'local_names' => 'Português'],
            'qu' => ['group' => 'Quechuan', 'name' => 'Quechua', 'local_names' => 'Runa Simi, Kichwa'],
            'rm' => ['group' => 'Indo-European', 'name' => 'Romansh', 'local_names' => 'Rumantsch Grischun'],
            'rn' => ['group' => 'Niger–Congo', 'name' => 'Rundi', 'local_names' => 'Ikirundi'],
            'ro' => ['group' => 'Indo-European', 'name' => 'Romanian, Moldavian, Moldovan', 'local_names' => 'Română'],
            'ru' => ['group' => 'Indo-European', 'name' => 'Russian', 'local_names' => 'русский'],
            'sa' => ['group' => 'Indo-European', 'name' => 'Sanskrit', 'local_names' => 'संस्कृतम्'],
            'sc' => ['group' => 'Indo-European', 'name' => 'Sardinian', 'local_names' => 'sardu'],
            'sd' => ['group' => 'Indo-European', 'name' => 'Sindhi', 'local_names' => 'सिन्धी, سنڌي، سندھی‎'],
            'se' => ['group' => 'Uralic', 'name' => 'Northern Sami', 'local_names' => 'Davvisámegiella'],
            'sm' => ['group' => 'Austronesian', 'name' => 'Samoan', 'local_names' => 'gagana fa\'a Samoa'],
            'sg' => ['group' => 'Creole', 'name' => 'Sango', 'local_names' => 'yângâ tî sängö'],
            'sr' => ['group' => 'Indo-European', 'name' => 'Serbian', 'local_names' => 'српски језик'],
            'gd' => ['group' => 'Indo-European', 'name' => 'Gaelic, Scottish Gaelic', 'local_names' => 'Gàidhlig'],
            'sn' => ['group' => 'Niger–Congo', 'name' => 'Shona', 'local_names' => 'chiShona'],
            'si' => ['group' => 'Indo-European', 'name' => 'Sinhala, Sinhalese', 'local_names' => 'සිංහල'],
            'sk' => ['group' => 'Indo-European', 'name' => 'Slovak', 'local_names' => 'Slovenčina, Slovenský Jazyk'],
            'sl' => ['group' => 'Indo-European', 'name' => 'Slovenian', 'local_names' => 'Slovenski Jezik, Slovenščina'],
            'so' => ['group' => 'Afro-Asiatic', 'name' => 'Somali', 'local_names' => 'Soomaaliga, af Soomaali'],
            'st' => ['group' => 'Niger–Congo', 'name' => 'Southern Sotho', 'local_names' => 'Sesotho'],
            'es' => ['group' => 'Indo-European', 'name' => 'Spanish, Castilian', 'local_names' => 'Español'],
            'su' => ['group' => 'Austronesian', 'name' => 'Sundanese', 'local_names' => 'Basa Sunda'],
            'sw' => ['group' => 'Niger–Congo', 'name' => 'Swahili', 'local_names' => 'Kiswahili'],
            'ss' => ['group' => 'Niger–Congo', 'name' => 'Swati', 'local_names' => 'SiSwati'],
            'sv' => ['group' => 'Indo-European', 'name' => 'Swedish', 'local_names' => 'Svenska'],
            'ta' => ['group' => 'Dravidian', 'name' => 'Tamil', 'local_names' => 'தமிழ்'],
            'te' => ['group' => 'Dravidian', 'name' => 'Telugu', 'local_names' => 'తెలుగు'],
            'tg' => ['group' => 'Indo-European', 'name' => 'Tajik', 'local_names' => 'тоҷикӣ, toçikī, تاجیکی‎'],
            'th' => ['group' => 'Tai–Kadai', 'name' => 'Thai', 'local_names' => 'ไทย'],
            'ti' => ['group' => 'Afro-Asiatic', 'name' => 'Tigrinya', 'local_names' => 'ትግርኛ'],
            'bo' => ['group' => 'Sino-Tibetan', 'name' => 'Tibetan', 'local_names' => 'བོད་ཡིག'],
            'tk' => ['group' => 'Turkic', 'name' => 'Turkmen', 'local_names' => 'Türkmen, Түркмен'],
            'tl' => ['group' => 'Austronesian', 'name' => 'Tagalog', 'local_names' => 'Wikang Tagalog'],
            'tn' => ['group' => 'Niger–Congo', 'name' => 'Tswana', 'local_names' => 'Setswana'],
            'to' => ['group' => 'Austronesian', 'name' => 'Tonga (Tonga Islands)', 'local_names' => 'Faka Tonga'],
            'tr' => ['group' => 'Turkic', 'name' => 'Turkish', 'local_names' => 'Türkçe'],
            'ts' => ['group' => 'Niger–Congo', 'name' => 'Tsonga', 'local_names' => 'Xitsonga'],
            'tt' => ['group' => 'Turkic', 'name' => 'Tatar', 'local_names' => 'татар теле, tatar tele'],
            'tw' => ['group' => 'Niger–Congo', 'name' => 'Twi', 'local_names' => 'Twi'],
            'ty' => ['group' => 'Austronesian', 'name' => 'Tahitian', 'local_names' => 'Reo Tahiti'],
            'ug' => ['group' => 'Turkic', 'name' => 'Uighur, Uyghur', 'local_names' => 'ئۇيغۇرچە‎, Uyghurche'],
            'uk' => ['group' => 'Indo-European', 'name' => 'Ukrainian', 'local_names' => 'Українська'],
            'ur' => ['group' => 'Indo-European', 'name' => 'Urdu', 'local_names' => 'اردو'],
            'uz' => ['group' => 'Turkic', 'name' => 'Uzbek', 'local_names' => 'Oʻzbek, Ўзбек, أۇزبېك‎'],
            've' => ['group' => 'Niger–Congo', 'name' => 'Venda', 'local_names' => 'Tshivenḓa'],
            'vi' => ['group' => 'Austroasiatic', 'name' => 'Vietnamese', 'local_names' => 'Tiếng Việt'],
            'vo' => ['group' => 'Constructed', 'name' => 'Volapük', 'local_names' => 'Volapük'],
            'wa' => ['group' => 'Indo-European', 'name' => 'Walloon', 'local_names' => 'Walon'],
            'cy' => ['group' => 'Indo-European', 'name' => 'Welsh', 'local_names' => 'Cymraeg'],
            'wo' => ['group' => 'Niger–Congo', 'name' => 'Wolof', 'local_names' => 'Wollof'],
            'fy' => ['group' => 'Indo-European', 'name' => 'Western Frisian', 'local_names' => 'Frysk'],
            'xh' => ['group' => 'Niger–Congo', 'name' => 'Xhosa', 'local_names' => 'isiXhosa'],
            'yi' => ['group' => 'Indo-European', 'name' => 'Yiddish', 'local_names' => 'ייִדיש'],
            'yo' => ['group' => 'Niger–Congo', 'name' => 'Yoruba', 'local_names' => 'Yorùbá'],
            'za' => ['group' => 'Tai–Kadai', 'name' => 'Zhuang, Chuang', 'local_names' => 'Saɯ cueŋƅ, Saw cuengh']];
    }

}
