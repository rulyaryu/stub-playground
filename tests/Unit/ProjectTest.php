<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Project;

class ProjectTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic unit test example.
     *
     * @return void
     */

    /** @test */  
    public function it_has_path()
    {
        $project = factory(Project::class)->create();

        $this->assertEquals('/projects/' . $project->id, $project->path());
    }
}
