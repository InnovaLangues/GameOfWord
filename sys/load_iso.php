<?php

require_once './sys/db.class.php';//probably useless
class IsoLang
{
    private $db;
    private static $common_languages = array('en' => 'Anglais',
                                                            'fr' => 'Français',
                                                            'it' => 'Italien',
                                                            'es' => 'Espagnol',
                                                            'zh' => 'Chinois', );

    public $commonlyUsed = ['Allemand', 'Anglais', 'Arabe', 'Chinois', 'Espagnol', 'Français', 'Hindi', 'Russe'];

    public function __construct()
    {
        $this->db = db::getInstance();
    }

    public function language_code_for($french)
    {
        $code = array_search($french, self::$common_languages, true);
        if ($code === false) {
            $this->db->query("SELECT `iso_code` FROM `langues` WHERE `french`='".$french."'");
            if ($this->db->affected_rows() == 1) {
                $code = $this->db->fetch_object()->iso_code;
            } else {
                $code = false;
            }
        }

        return $code;
    }

    public function any_to_iso($any)
    {
        $res = $this->language_code_for($any);
        if ($res === false) {
            if ($this->french_for($any) !== false) {
                $res = $any;
            } else {
                throw new Exception("$any n'est ni une langue en français, ni un code iso.");
            }
        }

        return $res;
    }

    public function any_to_french($any)
    {
        $res = $this->french_for($any);
        if ($res === false) {
            if ($this->language_code_for($any) !== false) {
                $res = $any;
            } else {
                throw new Exception("$any n'est ni une langue en français, ni un code iso.");
            }
        }

        return $res;
    }

    public function french_for($isoCode)
    {
        if (!isset(self::$common_languages[$isoCode])) {
            $this->db->query("SELECT `french` FROM `langues` WHERE `iso_code`='".
                    $isoCode."'");
            if ($this->db->affected_rows() == 1) {
                $french = $this->db->fetch_object()->french;
            } else {
                $french = false;
            }
        } else {
            $french = self::$common_languages[$isoCode];
        }

        return $french;
    }

    public function get_all_codes($language = 'french')
    {
        $res = array();
        $this->db->query("SELECT `iso_code`,`$language` FROM `langues`");
        if ($this->db->affected_rows() >= 1) {
            while ($tmpObj = $this->db->fetch_object()) {
                $res[$tmpObj->iso_code] = $tmpObj->{$language};
            }
        } else {
            $res = false;
        }

        return $res;
    }
}
