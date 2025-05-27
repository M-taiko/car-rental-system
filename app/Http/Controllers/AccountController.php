<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Log;

class AccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:account-list', ['only' => ['index', 'income', 'expenses', 'totals']]);
        $this->middleware('permission:account-create', ['only' => ['store', 'storeIncome', 'storeExpense']]);
        $this->middleware('permission:account-edit', ['only' => ['update']]);
        $this->middleware('permission:account-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $month = $request->input('month', date('Y-m'));
        $totalIncome = $this->calculateTotalIncome($month);
        $totalExpenses = $this->calculateTotalExpenses($month);
        $balance = $totalIncome - $totalExpenses;

        return view('accounts.index', compact('month', 'totalIncome', 'totalExpenses', 'balance'));
    }

    private function calculateTotalIncome($month)
    {
        return Account::where('type', 'income')
            ->whereYear('date', '=', substr($month, 0, 4))
            ->whereMonth('date', '=', substr($month, 5, 2))
            ->sum('amount');
    }

    private function calculateTotalExpenses($month)
    {
        return Account::where('type', 'expense')
            ->whereYear('date', '=', substr($month, 0, 4))
            ->whereMonth('date', '=', substr($month, 5, 2))
            ->sum('amount');
    }

    public function store(Request $request)
    {
        dd($request->all());

        try {
            Log::info('Received account store request:', $request->all());

            $validated = $request->validate([
                'type' => 'required|in:income,expense',
                'amount' => 'required|numeric|min:0',
                'description' => 'nullable|string',
                'date' => 'required|date',
            ]);

            Log::info('Validated data:', $validated);

            // تحويل التاريخ إلى timestamp
            $date = date('Y-m-d H:i:s', strtotime($validated['date']));

            $account = Account::create([
                'type' => $validated['type'],
                'amount' => $validated['amount'],
                'description' => $validated['description'],
                'date' => $date,
            ]);

            Log::info('Account created successfully:', $account->toArray());

            return response()->json([
                'success' => true,
                'message' => __('messages.account_added_successfully'),
                'account' => $account,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error:', $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error in store method:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while saving the account',
                'debug' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function show($id)
    {
        $account = Account::findOrFail($id);
        return view('accounts.show', compact('account'));
    }

    public function income(Request $request)
    {
        try {
            $month = $request->input('month', date('Y-m'));
            $query = Account::where('type', 'income')
                ->whereYear('date', '=', substr($month, 0, 4))
                ->whereMonth('date', '=', substr($month, 5, 2));

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('type', function ($account) {
                    return __('messages.income');
                })
                ->editColumn('date', function ($account) {
                    return date('Y-m-d H:i', strtotime($account->date));
                })
                ->make(true);
        } catch (\Exception $e) {
            Log::error('Error in income method:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'An error occurred while fetching income data'], 500);
        }
    }

    public function expenses(Request $request)
    {
        try {
            $month = $request->input('month', date('Y-m'));
            $query = Account::where('type', 'expense')
                ->whereYear('date', '=', substr($month, 0, 4))
                ->whereMonth('date', '=', substr($month, 5, 2));

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('type', function ($account) {
                    return __('messages.expense');
                })
                ->editColumn('date', function ($account) {
                    return date('Y-m-d H:i', strtotime($account->date));
                })
                ->make(true);
        } catch (\Exception $e) {
            Log::error('Error in expenses method:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'An error occurred while fetching expenses data'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $account = Account::findOrFail($id);
            $account->delete();

            return response()->json([
                'success' => true,
                'message' => __('messages.account_deleted_successfully'),
            ]);
        } catch (\Exception $e) {
            Log::error('Error in destroy method:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the account'
            ], 500);
        }
    }

    public function totals(Request $request)
    {
        try {
            $month = $request->input('month', date('Y-m'));
            $totalIncome = $this->calculateTotalIncome($month);
            $totalExpenses = $this->calculateTotalExpenses($month);
            $balance = $totalIncome - $totalExpenses;

            return response()->json([
                'totalIncome' => $totalIncome,
                'totalExpenses' => $totalExpenses,
                'balance' => $balance,
            ]);
        } catch (\Exception $e) {
            Log::error('Error in totals method:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'An error occurred while calculating totals'], 500);
        }
    }

    public function storeIncome(Request $request)
    {
        try {
            Log::info('Received income store request:', $request->all());

            $validated = $request->validate([
                'amount' => 'required|numeric|min:0',
                'description' => 'nullable|string',
                'date' => 'required|date',
            ]);

            Log::info('Validated income data:', $validated);

            $income = $this->createIncome(
                $validated['amount'],
                $validated['description'],
                $validated['date']
            );

            Log::info('Income created successfully:', $income->toArray());

            return response()->json([
                'success' => true,
                'message' => __('messages.income_added_successfully'),
                'account' => $income,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Income validation error:', $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error in storeIncome method:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while saving the income',
                'debug' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    private function createIncome($amount, $description, $date)
    {
        return Account::create([
            'type' => 'income',
            'amount' => $amount,
            'description' => $description,
            'date' => date('Y-m-d H:i:s', strtotime($date)),
        ]);
    }

    public function storeExpense(Request $request)
    {
        try {
            Log::info('Received expense store request:', $request->all());

            $validated = $request->validate([
                'amount' => 'required|numeric|min:0',
                'expense_type' => 'required|string',
                'description' => 'nullable|string',
                'date' => 'required|date',
            ]);

            Log::info('Validated expense data:', $validated);

            $expense = $this->createExpense(
                $validated['amount'],
                $validated['expense_type'],
                $validated['description'],
                $validated['date']
            );

            Log::info('Expense created successfully:', $expense->toArray());

            return response()->json([
                'success' => true,
                'message' => __('messages.expense_added_successfully'),
                'account' => $expense,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Expense validation error:', $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error in storeExpense method:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while saving the expense',
                'debug' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    private function createExpense($amount, $expenseType, $description, $date)
    {
        // Combine expense type with description
        $fullDescription = $expenseType;
        if (!empty($description)) {
            $fullDescription .= ' - ' . $description;
        }

        return Account::create([
            'type' => 'expense',
            'amount' => $amount,
            'description' => $fullDescription,
            'date' => date('Y-m-d H:i:s', strtotime($date)),
        ]);
    }
}
