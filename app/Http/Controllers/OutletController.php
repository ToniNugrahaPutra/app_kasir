<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Outlet;
use Illuminate\Support\Facades\Storage;

class OutletController extends Controller
{
    public function Qris(Request $request)
    {
        $request->validate([
            'qris' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);
        $outlet = Outlet::where('owner_id', auth()->id())->first();
        if (!$outlet) {
            return redirect()->back()->with('error', 'Outlet not found');
        }
        if ($request->file('qris')) {
            // Delete old image if exists
            if ($outlet->qris) {
                Storage::delete('public/qris/' . $outlet->qris);
            }
            $file = $request->file('qris');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            // Store file and get path
            $path = Storage::disk('public')->putFileAs('qris', $file, $filename);
            // Update outlet with filename
            $outlet->update([
                'qris' => $filename
            ]);
        }
        return redirect()->back()->with('success', 'QRIS image updated successfully');
    }
}