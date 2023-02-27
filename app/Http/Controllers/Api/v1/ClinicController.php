<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use Illuminate\Http\Request;

class ClinicController extends Controller
{
    public function index()
    {
        $clinics = Clinic::all();
        return response()->json(['clinics' => $clinics]);
    }

    public function store(Request $request)
    {
        $clinic = new Clinic;
        $clinic->name = $request->input('name');
        $clinic->address = $request->input('address');
        $clinic->admin_id = $request->input('admin_id');
        $clinic->save();

        return response()->json(['clinic' => $clinic]);
    }

    public function show($id)
    {
        $clinic = Clinic::find($id);

        if (!$clinic) {
            return response()->json(['error' => 'Clinic not found'], 404);
        }

        return response()->json(['clinic' => $clinic]);
    }

    public function update(Request $request, $id)
    {
        $clinic = Clinic::find($id);

        if (!$clinic) {
            return response()->json(['error' => 'Clinic not found'], 404);
        }

        $clinic->name = $request->input('name');
        $clinic->address = $request->input('address');
        $clinic->save();

        return response()->json(['clinic' => $clinic]);
    }

    public function destroy($id)
    {
        $clinic = Clinic::find($id);

        if (!$clinic) {
            return response()->json(['error' => 'Clinic not found'], 404);
        }

        $clinic->delete();

        return response()->json(['message' => 'Clinic deleted']);
    }
}
