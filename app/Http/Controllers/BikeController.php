<?php

namespace App\Http\Controllers;

use App\Models\Bike;
use Illuminate\Http\Request;
// use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Facades\DataTables;
// use DataTables;

class BikeController extends Controller
{
    public function index()
    {
        return view('bikes.index');
    }

    public function getBikesData(Request $request)
    {
        if ($request->ajax()) {
            $data = Bike::select(['id', 'name', 'type', 'color', 'price_per_hour', 'status']);
            return DataTables::of($data)
                ->addIndexColumn() // بيضيف عمود DT_RowIndex للعرض فقط
                ->addColumn('action', function ($row) {
                    $editBtn = '<button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editBikeModal'.$row->id.'"><i class="fas fa-edit"></i> '.trans('messages.edit').'</button>';
                    $deleteBtn = '<form action="'.route('bikes.destroy', $row->id).'" method="POST" style="display:inline-block;">'
                                .csrf_field().method_field('DELETE')
                                .'<button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> '.trans('messages.delete').'</button>'
                                .'</form>';
                    return $editBtn . ' ' . $deleteBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function store(Request $request)
    {
       $clear =  $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'color' => 'required|string|max:255',
            'price_per_hour' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);
        // dd($request->all());

        Bike::create($request->all());
        return redirect()->route('bikes.index')->with('success', 'تم إضافة الدراجة بنجاح');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'color' => 'required|string|max:255',
            'price_per_hour' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $bike = Bike::findOrFail($id);
        $bike->update($request->all());
        return redirect()->route('bikes.index')->with('success', 'تم تعديل الدراجة بنجاح');
    }

    public function destroy($id)
    {
        $bike = Bike::findOrFail($id);
        $bike->delete();
        return redirect()->route('bikes.index')->with('success', 'تم حذف الدراجة بنجاح');
    }
}
