<<<<<<< HEAD
<?php

declare(strict_types=1);

namespace Modules\Xot\Models\Policies;

use Modules\Xot\Contracts\ModelContract;
use Modules\Xot\Contracts\UserContract;

/**
 * Class MetatagPolicy.
 */
class MetatagPolicy extends XotBasePolicy {
    public function storeFileMetatag(UserContract $user, ModelContract $post): bool {
        //return ($metatag->tennant_name=='foodlocal');
        return false;
    }
}
=======
<?php

declare(strict_types=1);

namespace Modules\Xot\Models\Policies;

use Modules\Xot\Contracts\ModelContract;
use Modules\Xot\Contracts\UserContract;

/**
 * Class MetatagPolicy.
 */
class MetatagPolicy extends XotBasePolicy {
    public function storeFileMetatag(UserContract $user, ModelContract $post): bool {
        //return ($metatag->tennant_name=='foodlocal');
        return false;
    }
}
>>>>>>> 3c97c308c85924a62f31c89c71edfe23450749f0
