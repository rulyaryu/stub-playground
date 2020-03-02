<?php


namespace Tests\Setup;

use App\Project;
use App\Role;


class UserFactory
{
    protected $withProjectCount = 0;

    protected $ownedByRole;


        /**
     * @param int $withProjectCount
     * @return UserFactory
     */
    public function withProjects(int $withProjectCount): UserFactory
    {
        $this->withProjectCount = $withProjectCount;
        return $this;
    }

        public function setOwnerRole( Role $owner): UserFactory
    {
        $this->ownedByRole = $owner;
        return $this;
    }


    public function create()
    {
        $instance = factory(UserFactory::class)->create();

        factory(Project::class, $this->withProjectCount)->create(['instance_id' => $instance->id]);

        factory(Role::class)->create()->user()->associate($instance);


        return $instance;
    }
}
