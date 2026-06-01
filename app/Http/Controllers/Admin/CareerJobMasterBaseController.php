<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

abstract class CareerJobMasterBaseController extends Controller
{
    protected abstract function getModel(): Model;
    protected abstract function getRouteName(): string;
    protected abstract function getTitle(): string;

    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 20);
        $search = $request->query('search');

        $query = $this->getModel()::orderBy('id', 'asc');

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $items = $query->paginate($perPage)->withQueryString();
        $title = $this->getTitle();
        $routeName = $this->getRouteName();
        
        return view('admin.career_master.index', compact('items', 'title', 'routeName', 'search', 'perPage'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:' . $this->getModel()->getTable() . ',name',
        ]);

        $this->getModel()::create($request->all());

        return redirect()->back()->with('success', $this->getTitle() . ' created successfully.');
    }

    public function update(Request $request, $id)
    {
        $item = $this->getModel()::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255|unique:' . $this->getModel()->getTable() . ',name,' . $id,
        ]);

        $item->update($request->all());

        return redirect()->back()->with('success', $this->getTitle() . ' updated successfully.');
    }

    public function destroy($id)
    {
        $item = $this->getModel()::findOrFail($id);
        $item->delete();

        return redirect()->back()->with('success', $this->getTitle() . ' deleted successfully.');
    }
}
