<?php

declare(strict_types=1);

namespace Modules\Xot\Jobs\PanelCrud;

use Modules\Xot\Contracts\PanelContract;

//----------- Requests ----------
//------------ services ----------

/**
 * Class DestroyJob.
 */
class DestroyJob extends XotBaseJob
{
    public function handle(): PanelContract
    {
        $row = $this->panel->getRow();
        //per cancellare tabelle collegate esempio se cancello "profilo" voglio cancellare anche "utente"
        if (method_exists($this->panel, 'destroyCallback')) {
            $this->panel->destroyCallback(['row' => $row]);
        }

        $res = $row->delete();
        if ($res) {
            \Session::flash('status', 'eliminato');
        } else {
            \Session::flash('status', 'NON eliminato');
        }

        return $this->panel;
    }
}
