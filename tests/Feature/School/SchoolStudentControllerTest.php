<?php

namespace Tests\Feature\School;

use App\Models\Card;
use App\Models\School;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SchoolStudentControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $schoolOwner;
    protected $school;

    protected function setUp(): void
    {
        parent::setUp();

        // Roles
        Role::firstOrCreate(['name' => 'school-owner']);

        // School
        $this->schoolOwner = User::factory()->create();
        $this->schoolOwner->roles()->attach(Role::where('name', 'school-owner')->first()->role_id);
        $this->school = School::factory()->create(['user_id' => $this->schoolOwner->id]);
    }

    /** @test */
    public function it_lists_only_students_who_activated_school_cards()
    {
        // One student for this school
        $student = User::factory()->create(['name' => 'John Doe']);
        Card::factory()->create([
            'holder_id' => $student->id,
            'owner_type' => School::class,
            'owner_id' => $this->school->school_id,
        ]);

        // Another student for a different school
        $anotherSchool = School::factory()->create();
        $otherStudent = User::factory()->create(['name' => 'Jane Smith']);
        Card::factory()->create([
            'holder_id' => $otherStudent->id,
            'owner_type' => School::class,
            'owner_id' => $anotherSchool->school_id,
        ]);

        $response = $this->actingAs($this->schoolOwner)
            ->getJson(route('school.students.index'));

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data.students');
        $response->assertJsonStructure([
            'success',
            'data' => [
                'students',
                'pagination'
            ]
        ]);
    }

    /** @test */
    public function it_can_search_school_students()
    {
        $student = User::factory()->create(['name' => 'Search Target']);
        Card::factory()->create([
            'holder_id' => $student->id,
            'owner_type' => School::class,
            'owner_id' => $this->school->school_id,
        ]);

        $response = $this->actingAs($this->schoolOwner)
            ->getJson(route('school.students.index', ['search' => 'Target']));

        $response->assertJsonCount(1, 'data.students');
        $this->assertEquals('Search Target', $response->json('data.students.0.name'));
    }

    /** @test */
    public function it_allows_school_owner_to_view_student_details()
    {
        $student = User::factory()->create(['name' => 'John Doe']);
        Card::factory()->create([
            'holder_id' => $student->id,
            'owner_type' => School::class,
            'owner_id' => $this->school->school_id,
        ]);

        $response = $this->actingAs($this->schoolOwner)
            ->getJson(route('school.students.show', $student->id));

        $response->assertStatus(200);
        $response->assertJsonPath('data.student.name', 'John Doe');
    }

    /** @test */
    public function it_denies_access_to_students_not_linked_to_the_school()
    {
        $anotherSchool = School::factory()->create();
        $otherStudent = User::factory()->create(['name' => 'Jane Smith']);
        Card::factory()->create([
            'holder_id' => $otherStudent->id,
            'owner_type' => School::class,
            'owner_id' => $anotherSchool->school_id,
        ]);

        $response = $this->actingAs($this->schoolOwner)
            ->getJson(route('school.students.show', $otherStudent->id));

        $response->assertStatus(404);
    }

    /** @test */
    public function it_returns_404_when_viewing_unauthorized_student()
    {
        $otherStudent = User::factory()->create();
        // and no card for this school

        $response = $this->actingAs($this->schoolOwner)
            ->getJson(route('school.students.show', $otherStudent->id));

        $response->assertStatus(404);
    }
}
