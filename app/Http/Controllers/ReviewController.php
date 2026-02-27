<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    /**
     * Store a new review
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'comment' => 'required|string|min:10|max:2000',
            'pros' => 'nullable|array',
            'cons' => 'nullable|array',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'guest_name' => 'required_if:is_guest,true|string|max:255',
            'guest_email' => 'required_if:is_guest,true|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        
        try {
            // Check if user is guest (not logged in OR is_guest flag is true)
            $isGuest = !auth()->check() || $request->has('is_guest');
            $userId = auth()->check() ? auth()->id() : null;
            
            // For logged in users, check if they already reviewed
            if (!$isGuest && $userId) {
                $existingReview = Review::where('user_id', $userId)
                    ->where('product_id', $request->product_id)
                    ->first();
                    
                if ($existingReview) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You have already reviewed this product.'
                    ], 400);
                }
            }
            
            // Create review
            $review = new Review();
            $review->user_id = $userId; // This will be null for guests
            $review->product_id = $request->product_id;
            $review->rating = $request->rating;
            $review->title = $request->title;
            $review->comment = $request->comment;
            $review->pros = $request->pros;
            $review->cons = $request->cons;
            
            // Handle image uploads
            if ($request->hasFile('images')) {
                $imagePaths = [];
                foreach ($request->file('images') as $image) {
                    $path = $image->store('reviews/' . date('Y/m/d'), 'public');
                    $imagePaths[] = $path;
                }
                $review->images = $imagePaths;
            }
            
            // For guest users, set verified_purchase to false and store guest info
            if ($isGuest) {
                $review->verified_purchase = false;
                $review->guest_info = [
                    'name' => $request->guest_name,
                    'email' => $request->guest_email,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ];
                
                // Optional: Set status to false for moderation
                $review->status = true; // or false if you want to moderate guest reviews
                
            } else {
                // Check if user purchased this product
                $verifiedPurchase = Order::where('user_id', $userId)
                    ->whereHas('items', function($query) use ($request) {
                        $query->where('product_id', $request->product_id);
                    })
                    ->where('status', 'completed')
                    ->exists();
                
                $review->verified_purchase = $verifiedPurchase;
                $review->status = true; // Registered users reviews are auto-approved
            }
            
            $review->save();
            
            // Update product rating
            $this->updateProductRating($request->product_id);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => $isGuest ? 'Thank you for your review! It will be published after moderation.' : 'Thank you for your review!',
                'review' => $review
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit review: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Update product average rating
     */
    private function updateProductRating($productId)
    {
        $product = Product::find($productId);
        if ($product) {
            $avgRating = Review::where('product_id', $productId)
                ->where('status', true)
                ->avg('rating');
                
            $reviewsCount = Review::where('product_id', $productId)
                ->where('status', true)
                ->count();
                
            $product->rating = round($avgRating, 1);
            $product->reviews_count = $reviewsCount;
            $product->save();
        }
    }
    
    /**
     * Get product reviews (AJAX)
     */
    public function getProductReviews(Request $request, $productId)
    {
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 10);
        $sort = $request->get('sort', 'latest');
        $rating = $request->get('rating');
        
        $query = Review::with('user')
            ->where('product_id', $productId)
            ->where('status', true);
        
        // Filter by rating
        if ($rating && $rating != 'all') {
            $query->where('rating', $rating);
        }
        
        // Sort
        switch ($sort) {
            case 'highest':
                $query->orderBy('rating', 'desc');
                break;
            case 'lowest':
                $query->orderBy('rating', 'asc');
                break;
            case 'verified':
                $query->orderBy('verified_purchase', 'desc');
                break;
            case 'latest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }
        
        $reviews = $query->paginate($perPage, ['*'], 'page', $page);
        
        // Format reviews for response
        $formattedReviews = $reviews->map(function($review) {
            $userName = 'Guest';
            if ($review->user) {
                $userName = $review->user->name;
            } elseif ($review->guest_info && isset($review->guest_info['name'])) {
                $userName = $review->guest_info['name'];
            }
            
            return [
                'id' => $review->id,
                'rating' => $review->rating,
                'title' => $review->title,
                'comment' => $review->comment,
                'pros' => $review->pros,
                'cons' => $review->cons,
                'images' => $review->images,
                'verified_purchase' => $review->verified_purchase,
                'created_at' => $review->created_at->diffForHumans(),
                'created_at_formatted' => $review->created_at->format('M d, Y'),
                'user' => [
                    'name' => $userName,
                    'avatar' => $review->user->avatar ?? null,
                    'is_guest' => !$review->user
                ]
            ];
        });
        
        return response()->json([
            'success' => true,
            'reviews' => $formattedReviews,
            'total' => $reviews->total(),
            'current_page' => $reviews->currentPage(),
            'last_page' => $reviews->lastPage(),
            'per_page' => $reviews->perPage()
        ]);
    }
    
    /**
     * Get review statistics for a product
     */
    public function getReviewStats($productId)
    {
        $stats = [
            'total' => 0,
            'average' => 0,
            'ratings' => [
                5 => 0,
                4 => 0,
                3 => 0,
                2 => 0,
                1 => 0
            ],
            'percentage' => [
                5 => 0,
                4 => 0,
                3 => 0,
                2 => 0,
                1 => 0
            ]
        ];
        
        $reviews = Review::where('product_id', $productId)
            ->where('status', true)
            ->select('rating', DB::raw('count(*) as count'))
            ->groupBy('rating')
            ->get();
        
        $total = $reviews->sum('count');
        $stats['total'] = $total;
        
        if ($total > 0) {
            $sum = $reviews->sum(function($item) {
                return $item->rating * $item->count;
            });
            $stats['average'] = round($sum / $total, 1);
            
            foreach ($reviews as $review) {
                $stats['ratings'][$review->rating] = $review->count;
                $stats['percentage'][$review->rating] = round(($review->count / $total) * 100);
            }
        }
        
        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }
}