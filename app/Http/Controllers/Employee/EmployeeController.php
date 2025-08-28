<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\StoreEmployeeRequest;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $q = Employee::query();

        if ($s = $request->query('search')) {
            $q->where(function ($qq) use ($s) {
                $qq->where('first_name', 'like', "%{$s}%")
                   ->orWhere('last_name', 'like', "%{$s}%")
                   ->orWhere('job_title', 'like', "%{$s}%")
                   ->orWhere('department', 'like', "%{$s}%");
            });
        }

        $perPage = (int)($request->query('per_page', 12));
        $perPage = max(1, min(100, $perPage));

        return response()->json(
            $q->latest()->paginate($perPage)->appends($request->query())
        );
    }

    public function show(Employee $employee)
    {
        return response()->json($employee);
    }

    public function store(StoreEmployeeRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('employees', 'public');
            $data['photo'] = 'storage/'.$path;
        }

        $employee = Employee::create($data);

        return response()->json([
            'message' => 'Employee created',
            'employee' => $employee,
        ], 201);
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {
        $data = $request->validated();

        $oldPhoto = $employee->photo;

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('employees', 'public');
            $data['photo'] = 'storage/'.$path;

            if ($oldPhoto && str_starts_with($oldPhoto, 'storage/')) {
                Storage::disk('public')->delete(str_replace('storage/', '', $oldPhoto));
            }
        }

        $employee->update($data);

        return response()->json([
            'message' => 'Employee updated',
            'employee' => $employee,
        ]);
    }


    public function destroy(Employee $employee)
    {
        if ($employee->photo && str_starts_with($employee->photo, 'storage/')) {
            Storage::disk('public')->delete(str_replace('storage/', '', $employee->photo));
        }

        $employee->delete();

        return response()->json(['message' => 'Employee deleted']);
    }
}
