<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index()
    {
        $products = Product::latest()->paginate(10);
        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        return view('admin.products.create');
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'thumbnail'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'price'       => 'required|numeric|min:0',
            'file_path'   => 'nullable|file|mimes:pdf,zip,mp4|max:10240',
            'status'      => ['required', Rule::in(Product::STATUS_DRAFT, Product::STATUS_PUBLISHED, Product::STATUS_ARCHIVED)],
            'is_active'   => 'boolean',
            'metadata'    => 'nullable|array',
        ]);

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        // Handle file upload
        if ($request->hasFile('file_path')) {
            $validated['file_path'] = $request->file('file_path')->store('products', 'public');
        }

        $validated['user_id'] = auth()->id();

        Product::create($validated);

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        return view('admin.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'thumbnail'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'price'       => 'required|numeric|min:0',
            'file_path'   => 'nullable|file|mimes:pdf,zip,mp4|max:10240',
            'status'      => ['required', Rule::in(Product::STATUS_DRAFT, Product::STATUS_PUBLISHED, Product::STATUS_ARCHIVED)],
            'is_active'   => 'boolean',
            'metadata'    => 'nullable|array',
        ]);

        // Update thumbnail
        if ($request->hasFile('thumbnail')) {
            if ($product->thumbnail) {
                Storage::disk('public')->delete($product->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        // Update file
        if ($request->hasFile('file_path')) {
            if ($product->file_path) {
                Storage::disk('public')->delete($product->file_path);
            }
            $validated['file_path'] = $request->file('file_path')->store('products', 'public');
        }

        $product->update($validated);

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product)
    {
        if ($product->thumbnail) {
            Storage::disk('public')->delete($product->thumbnail);
        }
        if ($product->file_path) {
            Storage::disk('public')->delete($product->file_path);
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }

    /**
     * Toggle product active status.
     */
    public function toggleActive(Product $product)
    {
        $product->update(['is_active' => !$product->is_active]);
        return redirect()->back()->with('success', 'Product status updated.');
    }

    /**
     * Publish the product.
     */
    public function publish(Product $product)
    {
        $product->update(['status' => Product::STATUS_PUBLISHED]);
        return redirect()->back()->with('success', 'Product published.');
    }

    /**
     * Archive the product.
     */
    public function archive(Product $product)
    {
        $product->update(['status' => Product::STATUS_ARCHIVED]);
        return redirect()->back()->with('success', 'Product archived.');
    }


    // PUBLIC-FACING METHODS (no admin middleware)
    public function publicIndex()
    {
        $products = Product::where('status', 'published')
            ->where('is_active', true)
            ->latest()
            ->paginate(12);

        return view('products.index', compact('products'));
    }

    public function publicShow(Product $product)
    {
        // Ensure only published products can be viewed
        if ($product->status !== 'published' || !$product->is_active) {
            abort(404);
        }

        return view('products.show', compact('product'));
    }

    public function download(Product $product)
{
    $user = auth()->user();

    if(!$user->hasPaid($product->id)){
        return redirect()->route('products.show', $product)
                         ->with('error', 'You must complete payment first.');
    }

    return response()->download(storage_path('app/public/' . $product->file_path));
}

}
