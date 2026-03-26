<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ResourceCategory;
use App\Models\GeneralResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ResourceManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $student;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup roles and permissions
        $adminRole = Role::create(['name' => 'Admin']);
        $studentRole = Role::create(['name' => 'Student']);
        $permission = Permission::create(['name' => 'manage-courses']);
        $adminRole->givePermissionTo($permission);

        $this->admin = User::factory()->create();
        $this->admin->assignRole($adminRole);

        $this->student = User::factory()->create();
        $this->student->assignRole($studentRole);
    }

    public function test_admin_can_view_resource_categories()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.resource-categories.index'));
        $response->assertStatus(200);
    }

    public function test_admin_can_create_resource_category()
    {
        $response = $this->actingAs($this->admin)->post(route('admin.resource-categories.store'), [
            'name' => 'Test Category',
            'order_column' => 5,
            'status' => 1
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('resource_categories', ['name' => 'Test Category']);
    }

    public function test_admin_can_create_general_resource()
    {
        $category = ResourceCategory::create(['name' => 'Marketing']);

        $response = $this->actingAs($this->admin)->post(route('admin.general-resources.store'), [
            'category_id' => $category->id,
            'title' => 'Test Flyer',
            'link_url' => 'https://example.com/flyer',
            'icon' => 'fa-file-pdf',
            'order_column' => 1,
            'status' => 1
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('general_resources', ['title' => 'Test Flyer']);
    }

    public function test_student_can_view_resources_page_with_categories()
    {
        $category = ResourceCategory::create(['name' => 'Training', 'status' => true]);
        GeneralResource::create([
            'category_id' => $category->id,
            'title' => 'Training PDF',
            'link_url' => 'https://example.com/training',
            'status' => true
        ]);

        $response = $this->actingAs($this->student)->get(route('student.resources'));
        $response->assertStatus(200);
        $response->assertSee('Training');
        $response->assertSee('Training PDF');
    }

    public function test_inactive_resources_are_not_visible_to_students()
    {
        $category = ResourceCategory::create(['name' => 'Hidden Category', 'status' => false]);
        GeneralResource::create([
            'category_id' => $category->id,
            'title' => 'Hidden Resource',
            'link_url' => 'https://example.com/hidden',
            'status' => true
        ]);

        $response = $this->actingAs($this->student)->get(route('student.resources'));
        $response->assertStatus(200);
        $response->assertDontSee('Hidden Category');
        $response->assertDontSee('Hidden Resource');
    }
}
