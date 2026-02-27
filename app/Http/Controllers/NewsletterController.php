<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class NewsletterController extends Controller
{
    /**
     * Subscribe to newsletter
     */
    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:newsletter_subscribers,email',
            'name' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Generate verification token
            $token = Str::random(64);

            // Create subscriber
            $subscriber = NewsletterSubscriber::create([
                'email' => $request->email,
                'name' => $request->name,
                'is_active' => true, // or false if you want email verification
                'verification_token' => $token,
                'verified_at' => now() // if no verification needed
            ]);

            // TODO: Send verification email if needed
            // $this->sendVerificationEmail($subscriber);

            return redirect()->back()->with('success', 'Successfully subscribed to our newsletter!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to subscribe. Please try again.')
                ->withInput();
        }
    }

    /**
     * Unsubscribe from newsletter
     */
    public function unsubscribe(Request $request, $email = null)
    {
        $email = $email ?? $request->email;

        $validator = Validator::make(['email' => $email], [
            'email' => 'required|email|exists:newsletter_subscribers,email'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', 'Invalid email address.');
        }

        try {
            $subscriber = NewsletterSubscriber::where('email', $email)->first();
            
            if ($subscriber) {
                $subscriber->update([
                    'is_active' => false
                ]);
                // Or delete permanently: $subscriber->delete();
            }

            return redirect()->route('home')->with('success', 'Successfully unsubscribed from newsletter.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to unsubscribe. Please try again.');
        }
    }

    /**
     * Verify email (if using verification)
     */
    public function verify($token)
    {
        $subscriber = NewsletterSubscriber::where('verification_token', $token)
            ->whereNull('verified_at')
            ->first();

        if (!$subscriber) {
            return redirect()->route('home')->with('error', 'Invalid verification token.');
        }

        $subscriber->update([
            'verified_at' => now(),
            'is_active' => true,
            'verification_token' => null
        ]);

        return redirect()->route('home')->with('success', 'Email verified successfully! You are now subscribed to our newsletter.');
    }

    /**
     * API endpoint for AJAX subscription
     */
    public function apiSubscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:newsletter_subscribers,email',
            'name' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $token = Str::random(64);

            $subscriber = NewsletterSubscriber::create([
                'email' => $request->email,
                'name' => $request->name,
                'is_active' => true,
                'verification_token' => $token,
                'verified_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Successfully subscribed to newsletter!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to subscribe. Please try again.'
            ], 500);
        }
    }

    /**
     * Get all subscribers (admin)
     */
    public function index()
    {
        $subscribers = NewsletterSubscriber::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.newsletter.index', compact('subscribers'));
    }

    /**
     * Export subscribers (admin)
     */
    public function export()
    {
        $subscribers = NewsletterSubscriber::where('is_active', true)->get();
        
        $csvFileName = 'newsletter-subscribers-' . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $csvFileName . '"',
        ];

        $columns = ['Email', 'Name', 'Subscribed Date'];

        $callback = function() use ($subscribers, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($subscribers as $subscriber) {
                fputcsv($file, [
                    $subscriber->email,
                    $subscriber->name,
                    $subscriber->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function destroy($id)
    {
        $subscriber = NewsletterSubscriber::findOrFail($id);
        $subscriber->delete();

        return redirect()->back()->with('success', 'Subscriber removed successfully.');
    }
}