<?php

declare(strict_types=1);

namespace Modules\Xot\Models;

// use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Tenant\Services\TenantService;
use Modules\Xot\Contracts\ModelProfileContract;
use Modules\Xot\Contracts\ModelWithUserContract;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Tags\HasTags;

/**
 * Modules\Xot\Models\Profile.
 *
 * @property int                                                                            $id
 * @property string|null                                                                    $post_type
 * @property \Illuminate\Support\Carbon|null                                                $created_at
 * @property \Illuminate\Support\Carbon|null                                                $updated_at
 * @property string|null                                                                    $created_by
 * @property string|null                                                                    $updated_by
 * @property string|null                                                                    $deleted_by
 * @property string|null                                                                    $first_name
 * @property string|null                                                                    $last_name
 * @property string|null                                                                    $email
 * @property string|null                                                                    $phone
 * @property string|null                                                                    $address
 * @property int|null                                                                       $user_id
 * @property string|null                                                                    $bio
 * @property string|null                                                                    $emails
 * @property string|null                                                                    $mobiles
 * @property string|null                                                                    $envelope_id
 * @property int|null                                                                       $is_signed
 * @property int                                                                            $company_selected_id
 * @property string                                                                         $company_data_requests
 * @property string|null                                                                    $nexi_transaction_code
 * @property \Illuminate\Database\Eloquent\Collection<int, \Modules\User\Models\Permission> $permissions
 * @property int|null                                                                       $permissions_count
 * @property \Illuminate\Database\Eloquent\Collection<int, \Modules\User\Models\Role>       $roles
 * @property int|null                                                                       $roles_count
 * @property \Illuminate\Database\Eloquent\Collection<int, \Spatie\Tags\Tag>                $tags
 * @property int|null                                                                       $tags_count
 * @property \Modules\User\Models\User|null                                                 $user
 *
 * @method static \Modules\Xot\Database\Factories\ProfileFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Profile  newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Profile  newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Profile  permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile  query()
 * @method static \Illuminate\Database\Eloquent\Builder|Profile  role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile  whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile  whereBio($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile  whereCompanyDataRequests($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile  whereCompanySelectedId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile  whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile  whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile  whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile  whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile  whereEmails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile  whereEnvelopeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile  whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile  whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile  whereIsSigned($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile  whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile  whereMobiles($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile  whereNexiTransactionCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile  wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile  wherePostType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile  whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile  whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile  whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile  withAllTags(\ArrayAccess|\Spatie\Tags\Tag|array|string $tags, ?string $type = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile  withAllTagsOfAnyType($tags)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile  withAnyTags(\ArrayAccess|\Spatie\Tags\Tag|array|string $tags, ?string $type = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile  withAnyTagsOfAnyType($tags)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile  withoutTags(\ArrayAccess|\Spatie\Tags\Tag|array|string $tags, ?string $type = null)
 *
 * @mixin IdeHelperProfile
 *
 * @property string|null $surname
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereSurname($value)
 *
 * @mixin \Eloquent
 */
class Profile extends BaseModel implements ModelWithUserContract, ModelProfileContract
{
    // spatie
    use HasRoles;
    use HasTags;

    /**
     * Undocumented variable.
     *
     * @var string
     */
    protected $guard_name = 'web';

    /**
     * @var string[]
     */
    protected $fillable = ['id', 'user_id'];

    /*
     * Undocumented function.
     */
    public function user(): BelongsTo
    {
        // $user = TenantService::model('user'); //no bisgna guardare dentro config(auth  etc etc
        // $user_class = \get_class($user);
        $user_class = getUserClass();

        return $this->belongsTo($user_class);
    }
}
