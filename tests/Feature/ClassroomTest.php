<?php

use App\Models\Classroom;
use App\Models\Student;
use Tests\TestCase;

test('admin student page retrieves and displays students from database', function () {
    /** @var TestCase $this */
    $student = Student::first();

    $response = $this->get('/admin/student');

    $response->assertOk();
    $response->assertViewIs('admin.student');
    $response->assertViewHas('students');
    if ($student) {
        $response->assertSee($student->nama);
    }
});

test('admin classroom page retrieves and displays classrooms from database', function () {
    /** @var TestCase $this */
    $classroom = Classroom::first();

    $response = $this->get('/admin/classroom');

    $response->assertOk();
    $response->assertViewIs('admin.classroom');
    $response->assertViewHas('classrooms');
    if ($classroom) {
        $response->assertSee($classroom->name);
        $response->assertSee($classroom->major);
    }
});

test('admin classroom detail page displays classroom and its students', function () {
    /** @var TestCase $this */
    $classroom = Classroom::first() ?? Classroom::factory()->has(Student::factory()->count(3))->create();

    $response = $this->get("/admin/classroom/{$classroom->id}");

    $response->assertOk();
    $response->assertViewIs('admin.classroom-detail');
    $response->assertViewHas('classroom');
    $response->assertViewHas('students');
    $response->assertSee($classroom->name);

    if ($firstStudent = $classroom->students->first()) {
        $response->assertSee($firstStudent->nama);
    }
});

test('admin can create a new student via post request', function () {
    /** @var TestCase $this */
    $classroom = Classroom::first() ?? Classroom::factory()->create();

    $response = $this->post('/admin/student', [
        'nama' => 'Budi Pratama',
        'nis' => '99988811',
        'classroom_id' => $classroom->id,
        'address' => 'Jl. Jenderal Sudirman No. 10',
    ]);

    $response->assertRedirect('/admin/student');
    $this->assertDatabaseHas('students', [
        'nama' => 'Budi Pratama',
        'nis' => '99988811',
        'classroom_id' => $classroom->id,
    ]);
});
