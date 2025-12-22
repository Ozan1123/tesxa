<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Guest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GuestController extends Controller
{
    public function index()
    {
        $guests = Guest::orderBy('created_at', 'desc')->get();
        return view('admin.index', compact('guests'));
    }

    public function store(Request $request)
    {
        // 1. Validation
        $request->validate([
            'name' => 'required|string|max:255',
            'guest_type' => 'required|string',
            'purpose' => 'required|string',
            'class_info' => 'nullable|string',
            'gender' => 'required|string',
            'image' => 'required|string', // Base64
        ]);

        try {
            // 2. Process Image
            $base64Image = $request->input('image');

            // Remove header if present (e.g., "data:image/png;base64,")
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
                $base64Image = substr($base64Image, strpos($base64Image, ',') + 1);
                $type = strtolower($type[1]); // jpg, png, etc.
                if (!in_array($type, ['jpg', 'jpeg', 'png'])) {
                    return response()->json(['success' => false, 'message' => 'Invalid image type'], 400);
                }
            } else {
                return response()->json(['success' => false, 'message' => 'Invalid image data'], 400);
            }

            $base64Image = base64_decode($base64Image);

            if ($base64Image === false) {
                return response()->json(['success' => false, 'message' => 'Base64 decode failed'], 400);
            }

            // Create Directory if not exists
            if (!Storage::disk('public')->exists('signatures')) {
                Storage::disk('public')->makeDirectory('signatures');
            }

            // Generate Filename
            $filename = time() . '_' . uniqid() . '.png';
            $path = 'signatures/' . $filename;

            // Save to Storage
            Storage::disk('public')->put($path, $base64Image);

            // 3. Save to Database
            $guest = Guest::create([
                'name' => $request->name,
                'guest_type' => $request->guest_type,
                'purpose' => $request->purpose,
                'class_info' => $request->class_info,
                'gender' => $request->gender,
                'photo_path' => $path,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Guest registered successfully',
                'data' => $guest
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a guest record and their photo
     */
    public function destroy($id)
    {
        try {
            $guest = Guest::findOrFail($id);

            // Delete the physical photo file if it exists
            if ($guest->photo_path && Storage::disk('public')->exists($guest->photo_path)) {
                Storage::disk('public')->delete($guest->photo_path);
            }

            // Delete the database record
            $guest->delete();

            return redirect()->route('admin.index')->with('success', 'Data tamu berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('admin.index')->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }
}
