<?php


namespace Tests\Setup;

use App\Project;


class UserFactory
{
    protected $withProjectCount = 0;


    //dummyMethods

    public function create()
    {
        $instance = factory(UserFactory::class)->create();


            factory(Project::class, $this->withProjectCount)->create(
            [
                'instance_id' => $instance->id,
            ]
        );


        return $instance;
    }
}
