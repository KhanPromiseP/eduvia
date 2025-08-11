<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Carbon;

class AdController extends Controller
{
    /**
     * Display a listing of ads with flexible filtering and pagination.
     */
    public function index(Request $request)
    {
        $query = Ad::with(['user', 'product']);

        // Filters: status, search by title, date range, placement, type
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active()->currentlyRunning();
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('placement')) {
            $query->where('placement', $request->placement);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('start_date')) {
            $start = Carbon::parse($request->start_date)->startOfDay();
            $query->where('start_at', '>=', $start);
        }

        if ($request->filled('end_date')) {
            $end = Carbon::parse($request->end_date)->endOfDay();
            $query->where('end_at', '<=', $end);
        }

        $ads = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        return view('admin.ads.index', compact('ads'));
    }

    /**
     * Show the form for creating a new ad.
     */
    public function create()
    {
        $products = Product::all();
        $users = User::all();

        return view('admin.ads.create', compact('products', 'users'));
    }

    /**
     * Store a newly created ad in storage.
     */
    public function store(Request $request)
    {
        $data = $this->validateAd($request);

        // Normalize boolean checkbox values: presence means true, absence means false
        $data['is_active'] = $request->has('is_active');
        $data['is_random'] = $request->has('is_random');

        // Decode targeting JSON string if provided
        if (!empty($data['targeting']) && is_string($data['targeting'])) {
            $decoded = json_decode($data['targeting'], true);
            $data['targeting'] = $decoded ?: null;
        }

        // Create the ad
        Ad::create($data);

        return redirect()->route('admin.ads.index')
            ->with('success', 'Ad created successfully.');
    }

    /**
     * Display the specified ad with analytics.
     */
    public function show(Ad $ad)
    {
        $analytics = [
            'total_views' => $ad->views()->count() ?? 0,
            'total_clicks' => $ad->clicks()->count() ?? 0,
            'ctr' => $this->calculateCTR($ad),
        ];

        return view('admin.ads.show', compact('ad', 'analytics'));
    }

    /**
     * Show the form for editing the specified ad.
     */
    public function edit(Ad $ad)
    {
        $products = Product::all();
        $users = User::all();

        return view('admin.ads.edit', compact('ad', 'products', 'users'));
    }

    /**
     * Update the specified ad in storage.
     */
    public function update(Request $request, Ad $ad)
    {
        $data = $this->validateAd($request, $ad->id);

        // Normalize boolean checkbox values
        $data['is_active'] = $request->has('is_active');
        $data['is_random'] = $request->has('is_random');

        // Decode targeting JSON string if provided
        if (!empty($data['targeting']) && is_string($data['targeting'])) {
            $decoded = json_decode($data['targeting'], true);
            $data['targeting'] = $decoded ?: null;
        }

        $ad->update($data);

        return redirect()->route('admin.ads.index')
            ->with('success', 'Ad updated successfully.');
    }

    /**
     * Remove the specified ad from storage.
     */
    public function destroy(Ad $ad)
    {
        $ad->delete();

        return redirect()->route('admin.ads.index')
            ->with('success', 'Ad deleted successfully.');
    }

    /**
     * Validate request data for storing/updating ads.
     */
    protected function validateAd(Request $request, $adId = null): array
    {
        $rules = [
            'user_id' => ['required', 'exists:users,id'],
            'product_id' => ['nullable', 'exists:products,id'],
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(['image', 'video', 'banner', 'js', 'popup', 'persistent', 'interstitial'])],
            'content' => ['required', 'string'],
            'link' => ['nullable', 'url', 'max:2048'],
            'start_at' => ['nullable', 'date'],
            'end_at' => ['nullable', 'date', 'after_or_equal:start_at'],
            'is_active' => ['nullable', 'boolean'],
            'placement' => ['nullable', 'string', 'max:255'],
            'targeting' => ['nullable', 'json'],
            'is_random' => ['nullable', 'boolean'],
        ];

        $messages = [
            'end_at.after_or_equal' => 'The end date must be a date after or equal to the start date.',
            'link.url' => 'The link must be a valid URL.',
        ];

        return $request->validate($rules, $messages);
    }

    /**
     * Calculate Click-Through Rate (CTR) for an ad (example).
     */
    protected function calculateCTR(Ad $ad): float
    {
        $views = $ad->views()->count() ?? 0;
        $clicks = $ad->clicks()->count() ?? 0;

        if ($views === 0) {
            return 0.0;
        }

        return round(($clicks / $views) * 100, 2);
    }
}
