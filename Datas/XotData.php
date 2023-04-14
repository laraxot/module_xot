<?php

declare(strict_types=1);

namespace Modules\Xot\Datas;

use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelData\Data;

/**
 * Undocumented class.
 */
class XotData extends Data
{
    public string $main_module; // => 'Blog'
    public string $param_name = 'noset';

    public string $adm_home = '01';
    public string $adm_theme; // ' => 'AdminLTE',
    // public bool $enable_ads;//' => '1',

    public string $primary_lang = 'it';
    // 'pub_theme' => 'DirectoryBs5',
    public string $pub_theme; // ' => 'BsItalia',
    public string $search_action = 'it/videos';
    public bool $show_trans_key = false;
    public string $register_type = '0';
    public string $verification_type = '';
    public bool $login_verified = false;

    public function getProfileClass(): string
    {
        $profile_class = 'Modules\\'.$this->main_module.'\Models\Profile';

        return $profile_class;
    }

    public function getProfileModelByUserId(string $user_id): Model
    {
        $profile_class = $this->getProfileClass();
        $profile = app($profile_class)->firstOrCreate(['user_id' => $user_id]);

        return $profile;
    }
}
