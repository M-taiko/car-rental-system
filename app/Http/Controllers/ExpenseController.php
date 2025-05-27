<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Account;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ExpenseController extends Controller
{
    public function index()
    {
        return view('expenses.index');
    }

    public function getExpensesData(Request $request)
    {
        try {
            if (!$request->ajax()) {
                return response()->json(['error' => 'Invalid request'], 400);
            }

            if (!auth()->check()) {
                return response()->json(['error' => 'Unauthorized. Please log in.'], 401);
            }

            $data = Account::where('type', 'expense')
                          ->select([
                              'id',
                              'type',
                              'amount',
                              'description',
                              'date'
                          ]);

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('type', function ($row) {
                    // Since we're getting data from accounts table, we need to parse the description
                    // to get the actual expense type
                    $description = explode(' - ', $row->description);
                    return $description[0] ?? $row->type;
                })
                ->editColumn('date', function ($row) {
                    return date('Y-m-d', strtotime($row->date));
                })
                ->addColumn('action', function ($row) {
                    $actions = [];

                    if (auth()->user()->can('view-expenses')) {
                        $actions[] = [
                            'url' => route('expenses.show', $row->id),
                            'icon' => 'fa-eye',
                            'class' => 'btn-info',
                            'label' => trans('messages.view')
                        ];
                    }

                    if (auth()->user()->can('edit-expenses')) {
                        $actions[] = [
                            'url' => 'javascript:void(0)',
                            'icon' => 'fa-edit',
                            'class' => 'btn-primary editExpense',
                            'label' => trans('messages.edit'),
                            'data-id' => $row->id
                        ];
                    }

                    if (auth()->user()->can('delete-expenses')) {
                        $actions[] = [
                            'url' => 'javascript:void(0)',
                            'icon' => 'fa-trash',
                            'class' => 'btn-danger deleteExpense',
                            'label' => trans('messages.delete'),
                            'data-id' => $row->id
                        ];
                    }

                    $actionButtons = '';
                    foreach ($actions as $action) {
                        $dataId = isset($action['data-id']) ? 'data-id="' . $action['data-id'] . '"' : '';
                        $actionButtons .= sprintf(
                            '<a href="%s" class="btn btn-sm %s mx-1" %s><i class="fas %s"></i> %s</a>',
                            $action['url'],
                            $action['class'],
                            $dataId,
                            $action['icon'],
                            $action['label']
                        );
                    }

                    return $actionButtons ?: trans('messages.no_actions');
                })
                ->rawColumns(['action'])
                ->escapeColumns(['description'])
                ->make(true);
        } catch (\Exception $e) {
            \Log::error('Error in getExpensesData: ' . $e->getMessage());
            return response()->json([
                'error' => 'An error occurred while fetching data',
                'details' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:' . implode(',', array_keys(Expense::getTypes())),
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'date' => 'required|date',
        ]);

        // Create account entry directly
        Account::create([
            'type' => 'expense',
            'amount' => $request->amount,
            'description' => Expense::getTypes()[$request->type] . ' - ' . ($request->description ?? trans('messages.general_expense')),
            'date' => $request->date,
        ]);

        return redirect()->route('expenses.index')->with('success', trans('messages.expense_added_successfully'));
    }

    public function show($id)
    {
        try {
            $account = Account::where('type', 'expense')->findOrFail($id);
            $description = explode(' - ', $account->description);
            $expense_type = $description[0] ?? '';
            $expense_description = $description[1] ?? '';

            return view('expenses.show', compact('account', 'expense_type', 'expense_description'));
        } catch (\Exception $e) {
            \Log::error('Error in expense show: ' . $e->getMessage());
            return redirect()->route('expenses.index')
                           ->with('error', trans('messages.expense_not_found'));
        }
    }

    public function edit($id)
    {
        $account = Account::where('type', 'expense')->find($id);
        if (!$account) {
            return response()->json([
                'success' => false,
                'message' => trans('messages.expense_not_found'),
            ], 404);
        }

        // Parse the description to get expense type and actual description
        $description = explode(' - ', $account->description);
        $data = [
            'id' => $account->id,
            'type' => array_search($description[0], Expense::getTypes()) ?: 'other',
            'amount' => $account->amount,
            'description' => $description[1] ?? '',
            'date' => $account->date,
        ];

        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        $account = Account::where('type', 'expense')->find($id);
        if (!$account) {
            return response()->json([
                'success' => false,
                'message' => trans('messages.expense_not_found'),
            ], 404);
        }

        $request->validate([
            'type' => 'required|in:' . implode(',', array_keys(Expense::getTypes())),
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'date' => 'required|date',
        ]);

        $account->update([
            'amount' => $request->amount,
            'description' => Expense::getTypes()[$request->type] . ' - ' . ($request->description ?? trans('messages.general_expense')),
            'date' => $request->date,
        ]);

        return response()->json([
            'success' => true,
            'message' => trans('messages.expense_updated_successfully'),
        ]);
    }

    public function destroy($id)
    {
        $account = Account::where('type', 'expense')->find($id);
        if (!$account) {
            return response()->json([
                'success' => false,
                'message' => trans('messages.expense_not_found'),
            ], 404);
        }

        $account->delete();

        return response()->json([
            'success' => true,
            'message' => trans('messages.expense_deleted_successfully'),
        ]);
    }
}
