<?php
namespace Modules\Xot\Commands;

use Illuminate\Console\Command;

//---- MODELS ----
use Modules\LU\Models\User;

use Illuminate\Foundation\Bus\Dispatchable;


class PurchasePodcast extends Command /*implements SelfHandling */{
    //use Dispatchable;
    protected $user, $podcast;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(User $user){
        /*
        Bus::map([
            ProcessPodcast::class => ProcessPodcastHandler::class,
        ]);
        */
        $this->user = $user;
        //$this->podcast = $podcast;
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle(){
        // Handle the logic to purchase the podcast...
        ddd('handle');
        //event(new PodcastWasPurchased($this->user, $this->podcast));
    }

}