<?php



namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactFormMail;
use App\Mail\UserConfirmationMail;
use Illuminate\Support\Facades\Validator;



class ContactController extends Controller
{
   public function index()
    {
        return view('contact.index'); 
    }

    
    /**
     * Handle the contact form submission.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function send(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'firstName' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string|max:5000',
            'service' => 'nullable|string|max:255',
            'newsletter' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Prepare form data
            $formData = $request->only(['firstName', 'email', 'message', 'service', 'newsletter']);
            
            // Send email to admin
            Mail::to(config('mail.to.address'))->send(new ContactFormMail($formData));
            
            // Send confirmation email to user
            Mail::to($formData['email'])->send(new UserConfirmationMail($formData));

            return response()->json([
                'success' => true,
                'message' => 'Your message has been sent successfully! Please check your email for confirmation.'
            ]);
        } catch (\Exception $e) {
            \Log::error('Contact form error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while sending your message. Please try again later.'
            ], 500);
        }
    }
}
