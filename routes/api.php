<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\SubmissionController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\TimetableController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\ParentController;
use App\Http\Controllers\PaymentController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| These routes are prefixed with /api and use the 'api' middleware group.
| They are stateless and ideal for frontend apps like React.
|--------------------------------------------------------------------------
*/

// Public route
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
// Protected routes (require token)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/students', [StudentController::class, 'index']);
    Route::post('/students', [StudentController::class, 'store']);
    Route::get('/students/{id}', [StudentController::class, 'show']);
    Route::put('/students/{id}', [StudentController::class, 'update']);
    Route::delete('/students/{id}', [StudentController::class, 'destroy']);
   Route::put('/teachers/{id}', [TeacherController::class, 'update']);
Route::delete('/teachers/{id}', [TeacherController::class, 'destroy']);

Route::put('/subjects/{id}', [SubjectController::class, 'update']);
Route::delete('/subjects/{id}', [SubjectController::class, 'destroy']);

Route::put('/classes/{id}', [ClassController::class, 'update']);
Route::delete('/classes/{id}', [ClassController::class, 'destroy']);
Route::post('/assignments/store', [AssignmentController::class, 'store']); // For creating
    Route::post('/assignments/{assignment}', [AssignmentController::class, 'update']); // For updating (Laravel will detect _method=PUT)
    Route::post('/logout', [AuthController::class, 'logout']); // optional
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/admissions', [StudentController::class, 'store']);
    Route::get('/students/export/excel', [StudentController::class, 'exportExcel']);
    Route::get('/subjects/{id}/students', [AttendanceController::class, 'studentsForSubject']);
    Route::post('/attendances/batch', [AttendanceController::class, 'storeBatch']);
});
Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/teachers', [TeacherController::class, 'index']);
    Route::post('/teachers', [TeacherController::class, 'store']);
    Route::post('/teachers/{id}/assign-subject', [TeacherController::class, 'assignSubject']);

    Route::get('/subjects', [SubjectController::class, 'index']);
    Route::post('/subjects', [SubjectController::class, 'store']);

    Route::get('/classes', [ClassController::class, 'index']);
    Route::post('/classes', [ClassController::class, 'store']);
});


Route::middleware(['auth:sanctum', 'role:teacher'])->group(function () {
    // Route::get('/teacher/profile', [TeacherController::class, 'showProfile']);
    // Route::put('/teacher/profile', [TeacherController::class, 'updateProfile']);
    // Route::get('/teacher/timetable', [TeacherController::class, 'getTimetable']);
});
Route::middleware('auth:sanctum')->get('/teacher/profile', [TeacherController::class, 'showProfile']);
Route::middleware('auth:sanctum')->put('/teacher/profile', [TeacherController::class, 'updateProfile']);
Route::middleware('auth:sanctum')->put('/teacher/timetable', [TeacherController::class, 'getTimetable']);
Route::middleware('auth:sanctum')->get('/teacher/subjects', [AttendanceController::class, 'teacherSubjects']);
    Route::get('/teacher/subjects', [AttendanceController::class, 'teacherSubjects']);
   
    Route::post('/attendances/batch', [AttendanceController::class, 'storeBatch']);

    Route::middleware('auth:sanctum')->group(function () {
    Route::get('/teacher/subjects', [AttendanceController::class, 'teacherSubjects']);
    Route::get('/subjects/{id}/students', [AttendanceController::class, 'studentsForSubject']);
    Route::post('/attendances/batch', [AttendanceController::class, 'storeBatch']);
});
Route::middleware('auth:sanctum')->post('/grades/store', [GradeController::class, 'storeGrade']);




Route::middleware('auth:sanctum')->group(function () {
    Route::post('/assignments/store', [AssignmentController::class, 'store']);
    Route::post('/assignments/submit', [AssignmentController::class, 'submit']);
    Route::post('/assignments/grade/{id}', [AssignmentController::class, 'grade']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/student/assignments', [AssignmentController::class, 'assignedToStudent']);
});

Route::middleware('auth:sanctum')->post('/student/assignments/submit', [AssignmentController::class, 'submit']);
Route::middleware('auth:sanctum')->group(function () {
    Route::put('/assignments/{id}', [AssignmentController::class, 'update']);
    Route::delete('/assignments/{id}', [AssignmentController::class, 'destroy']);
});
Route::middleware('auth:sanctum')->get('/teacher/assignments', [AssignmentController::class, 'teacherAssignments']);
Route::middleware('auth:sanctum')->get('/teacher/submissions', [SubmissionController::class, 'teacherSubmissions']);
Route::middleware('auth:sanctum')->post('/teacher/submissions/{id}/grade', [SubmissionController::class, 'grade']);

Route::middleware('auth:sanctum')->post('/teacher/assignments/submit', [AssignmentController::class, 'submitAssignment']);
// ✅ Get all attendance records for a specific student
Route::middleware('auth:sanctum')->get('/teacher/students/{studentId}/attendances', [AttendanceController::class, 'getStudentAttendances']);

// ✅ Get all students with their attendance records
Route::middleware('auth:sanctum')->get('/teacher/attendances/all', [AttendanceController::class, 'getAllStudentAttendances']);

// ✅ Optional: Get attendance summary for a subject/date
Route::middleware('auth:sanctum')->get('/teacher/attendance/summary', [AttendanceController::class, 'summary']);
Route::middleware('auth:sanctum')->get('/student/attendances', [AttendanceController::class, 'studentAttendances']);
Route::middleware('auth:sanctum')->get('/user', function () {
    return response()->json(Auth::user());
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/teacher/timetable', [TimetableController::class, 'index']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/admin/timetable', [TimetableController::class, 'store']);
});
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/admin/timetable', [TimetableController::class, 'adminIndex']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/teacher/timetable', [TimetableController::class, 'teacherTimetable']);
    Route::get('/teacher/classes', [TeacherController::class, 'getTeacherClasses']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/student/subjects', [StudentController::class, 'mySubjects']);
    Route::get('/students/{id}/grades', [GradeController::class, 'studentGrades']);
});



Route::middleware('auth:sanctum')->group(function () {
    // Student applies for leave
    Route::post('/leave/apply', [LeaveRequestController::class, 'apply']);

    // Student views their own leave requests
    Route::get('/leave/my-requests', [LeaveRequestController::class, 'studentRequests']);

    // Teacher views leave requests for subjects they teach
   Route::match(['get', 'patch'], '/leave/teacher-requests', [LeaveRequestController::class, 'RequestsforTeacher']);
    Route::get('/leave/all', [LeaveRequestController::class, 'allRequests']); // optional for admin
    // Teacher approves or rejects a leave request
    Route::patch('/leave/{id}/update-status', [LeaveRequestController::class, 'updateStatus']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/parent/children', [ParentController::class, 'getChildrenOverview']);
     Route::post('/pay/initiate', [PaymentController::class, 'initiate']);
     Route::get('/student/{id}/fee', [StudentController::class, 'getFee']);
});

Route::post('/pay/callback', [PaymentController::class, 'callback'])->name('telebirr.callback');
