<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Guest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use Inertia\Inertia;

class GuestController extends Controller
{
    // Admin Dashboard (Monitoring) - kept as "index" or "monitoring"
    public function index()
    {
        // This was "admin.index", let's make it the main Admin Dashboard
        return Inertia::render('Admin/Monitoring', [
            'activeVisits' => \App\Models\Visit::with('guest')->where('status', 'active')->get()
        ]);
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
            'face_descriptor' => 'nullable|string',
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
                'type' => 'general',
                'guest_type' => $request->guest_type,
                'purpose' => $request->purpose, // Keep purpose on guest for historical "last purpose" or just generic
                'class_info' => $request->class_info,
                'gender' => $request->gender,
                'photo_path' => $path,
                'face_descriptor' => $request->face_descriptor, // JSON String
            ]);

            // Create Visit Record
            $guest->visits()->create([
                'purpose' => $request->purpose,
                'status' => 'active'
            ]);

            return redirect()->back()->with('success', 'Guest registered successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['message' => 'Server Error: ' . $e->getMessage()]);
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

    public function apiGuests()
    {
        $guests = Guest::select('id', 'name', 'photo_path', 'purpose', 'created_at')
            ->where('type', 'vip') // Only VIPs
            ->whereNotNull('photo_path')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($guest) {
                return [
                    'id' => $guest->id,
                    'name' => $guest->name,
                    'purpose' => $guest->purpose,
                    'photo_url' => asset('storage/' . $guest->photo_path),
                    'registered_at' => $guest->created_at->format('H:i'), // Jam Masuk
                    'date' => $guest->created_at->format('d M Y')
                ];
            });

        return response()->json($guests);
    }




    // --- Admin Pages ---
    public function vip()
    {
        return Inertia::render('Admin/VIP', [
            'vips' => Guest::where('type', 'vip')
                ->with(['visits' => function ($query) {
                    $query->latest()->limit(1);
                }])
                ->orderBy('created_at', 'desc')
                ->get()
        ]);
    }

    public function storeVip(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'institution' => 'required',
            'photo' => 'required|image',
            'face_descriptor' => 'required' // Make it required to ensure scanner works
        ]);

        $path = $request->file('photo')->store('signatures', 'public');

        Guest::create([
            'name' => $request->name,
            'type' => 'vip',
            'guest_type' => 'Dinas', // Default for VIP
            'institution' => $request->institution,
            'purpose' => 'Kunjungan Dinas',
            'gender' => 'male',
            'photo_path' => $path,
            'face_descriptor' => $request->face_descriptor
        ]);

        return redirect()->route('admin.vip')->with('success', 'Tamu VIP Berhasil Ditambahkan');
    }

    // --- Hybrid Workflow Endpoints ---

    /**
     * API: Get all guest descriptors for frontend matcher
     */
    public function getDescriptors()
    {
        // Select only necessary fields. limit logic if needed (e.g. active guests only? no, we need all for recognition)
        // Check performance if 1000s of guests. For now, fetch all with valid descriptors.
        $guests = Guest::select('id', 'name', 'face_descriptor')
            ->where('type', 'vip')
            ->whereNotNull('face_descriptor')
            ->get();

        return response()->json($guests);
    }

    /**
     * API: Check status of recognized guest
     */
    public function checkStatus(Request $request)
    {
        $request->validate(['guest_id' => 'required|exists:guests,id']);

        $guest = Guest::find($request->guest_id);
        $activeVisit = $guest->visits()->where('status', 'active')->first();

        if ($activeVisit) {
            return response()->json([
                'status' => 'active',
                'guest' => $guest,
                'visit' => $activeVisit
            ]);
        }

        return response()->json([
            'status' => 'none',
            'guest' => $guest
        ]);
    }

    /**
     * API: Check-in (Create Visit)
     */
    public function checkin(Request $request)
    {
        $request->validate([
            'guest_id' => 'required|exists:guests,id',
            'purpose' => 'nullable|string'
        ]);

        $guest = Guest::find($request->guest_id);

        // Prevent double
        if ($guest->visits()->where('status', 'active')->exists()) {
            return response()->json(['success' => false, 'message' => 'Guest is already active']);
        }

        $visit = $guest->visits()->create([
            'purpose' => $request->purpose ?? $guest->purpose, // Use last purpose or input
            'status' => 'active',
            'check_in_at' => now()
        ]);

        return response()->json(['success' => true, 'visit' => $visit]);
    }

    /**
     * API: Check-out (Close Visit)
     */
    public function checkOut(Request $request)
    {
        $request->validate(['guest_id' => 'required|exists:guests,id']);

        $guest = Guest::find($request->guest_id);
        $visit = $guest->visits()->where('status', 'active')->first();

        if ($visit) {
            $visit->update([
                'status' => 'completed',
                'check_out_at' => now()
            ]);
            return response()->json(['success' => true, 'message' => 'Goodbye']);
        }

        return response()->json(['success' => false, 'message' => 'No active visit']);
    }

    // --- Admin Actions ---

    public function forceCheckout(Request $request, $id)
    {
        $visit = \App\Models\Visit::findOrFail($id);

        $checkoutTime = now();
        if ($request->has('check_out_time') && $request->check_out_time) {
            try {
                // Combine today's date with the provided time, interpreting it as Asia/Jakarta
                $checkoutTime = \Carbon\Carbon::createFromFormat('H:i', $request->check_out_time, 'Asia/Jakarta');
            } catch (\Exception $e) {
                // Fallback to now() if parsing fails
                $checkoutTime = now();
            }
        }

        $visit->update([
            'check_out_at' => $checkoutTime,
            'status' => 'forced_exit'
        ]);
        return back()->with('success', 'Visit forced checkout successfully');
    }

    // ... existing ...

    public function monitoring()
    {
        $activeVisits = \App\Models\Visit::with(['guest' => function ($query) {
            $query->withTrashed();
        }])
            ->where('status', 'active')
            ->orderBy('check_in_at', 'desc')
            ->get();

        $stats = [
            'total_today' => \App\Models\Visit::whereDate('check_in_at', now())->count(),
            'total_guests' => \App\Models\Guest::count(),
        ];

        return Inertia::render('Admin/Monitoring', [
            'activeVisits' => $activeVisits,
            'stats' => $stats
        ]);
    }

    public function reports()
    {
        $visits = \App\Models\Visit::with(['guest' => function ($query) {
            $query->withTrashed();
        }])
            ->orderBy('check_in_at', 'desc')
            ->paginate(20);

        return Inertia::render('Admin/Reports', [
            'visits' => $visits
        ]);
    }
}
