<?php

declare(strict_types=1);

namespace Modules\Xot\Contracts;

/**
 * Modules\Xot\Contracts\UserContract.
 *
 * @property int                                                                                                       $auth_user_id
 * @property string|null                                                                                               $handle
 * @property string|null                                                                                               $passwd
 * @property string|null                                                                                               $lastlogin
 * @property int|null                                                                                                  $owner_user_id
 * @property int|null                                                                                                  $owner_group_id
 * @property string|null                                                                                               $is_active
 * @property int|null                                                                                                  $enable
 * @property string|null                                                                                               $email
 * @property string|null                                                                                               $first_name
 * @property string|null                                                                                               $last_name
 * @property int|null                                                                                                  $ente
 * @property int|null                                                                                                  $matr
 * @property string|null                                                                                               $password
 * @property string|null                                                                                               $hash
 * @property string|null                                                                                               $activation_code
 * @property string|null                                                                                               $forgotten_password_code
 * @property \Illuminate\Support\Carbon|null                                                                           $last_login_at
 * @property string|null                                                                                               $last_login_ip
 * @property string|null                                                                                               $token_check
 * @property int|null                                                                                                  $is_verified
 * @property string|null                                                                                               $remember_token
 * @property \Illuminate\Support\Carbon|null                                                                           $email_verified_at
 * @property \Illuminate\Support\Carbon|null                                                                           $created_at
 * @property \Illuminate\Support\Carbon|null                                                                           $updated_at
 * @property string|null                                                                                               $created_by
 * @property string|null                                                                                               $updated_by
 * @property \Illuminate\Database\Eloquent\Collection|\Modules\LU\Models\AreaAdminArea[]                               $areaAdminAreas
 * @property int|null                                                                                                  $area_admin_areas_count
 * @property mixed                                                                                                     $full_name
 * @property mixed                                                                                                     $guid
 * @property \Illuminate\Database\Eloquent\Collection|\Modules\LU\Models\GroupUser[]                                   $groups
 * @property int|null                                                                                                  $groups_count
 * @property \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property int|null                                                                                                  $notifications_count
 * @property \Modules\LU\Models\PermUser|null                                                                          $perm
 * @property \Illuminate\Database\Eloquent\Collection|\Modules\LU\Models\PermUser[]                                    $permUsers
 * @property int|null                                                                                                  $perm_users_count
 * @property \Illuminate\Database\Eloquent\Collection|\Modules\LU\Models\PermUser[]                                    $perms
 * @property int|null                                                                                                  $perms_count
 * @property \Illuminate\Database\Eloquent\Collection|\Modules\LU\Models\UserRight[]                                   $rights
 * @property int|null                                                                                                  $rights_count
 * @property \Illuminate\Database\Eloquent\Collection|\Modules\LU\Models\SocialProvider[]                              $socialProviders
 * @property int|null                                                                                                  $social_providers_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereActivationCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAuthUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEnable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEnte($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereForgottenPasswordCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereHandle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIsVerified($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastLoginAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastLoginIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastlogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereMatr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereOwnerGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereOwnerUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePasswd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTokenCheck($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedBy($value)
 * @mixin \Eloquent
 *
 * @property \Modules\Blog\Models\Profile|null $profile
 * @property int|null                          $group_id
 * @property int|null                          $banned_id
 * @property int|null                          $country_id
 * @property int|null                          $question_id
 * @property string|null                       $nome
 * @property string|null                       $cognome
 * @property int|null                          $stabi
 * @property int|null                          $repar
 * @property string|null                       $provincia
 * @property string|null                       $conosciuto
 * @property string|null                       $news
 * @property string|null                       $citta
 * @property int|null                          $segno
 * @property int|null                          $hmail
 * @property int|null                          $bounce
 * @property string|null                       $dataIscrizione
 * @property int|null                          $dataCancellazione
 *
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBannedId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBounce($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCitta($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCognome($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereConosciuto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDataCancellazione($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDataIscrizione($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereHmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereNews($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereNome($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereProvincia($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRepar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereSegno($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereStabi($value)
 *
 * @property \Modules\Blog\Models\Profile|null $profile
 *
 * @method bool  hasRole($value)
 * @method mixed role($value)
 * @method mixed update($params)
 * @method mixed forceFill($params)
 */
interface UserContract {
    /*
    public function isSuperAdmin();
    public function name();
    public function areas();
    public function avatar();
    */
}