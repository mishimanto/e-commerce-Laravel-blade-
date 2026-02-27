<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Brand;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Show the About Us page.
     */
    public function about()
    {

        $custormers = User::count();
        $orders = Order::count(); 
        $brands = Brand::count();

        $stats = [
            ['value' => $custormers. '+', 'label' => 'Happy Customers'],
            ['value' => number_format($orders) . '+', 'label' => 'Products Sold'],
            ['value' => number_format($brands) . '+', 'label' => 'Brands Available'],
            ['value' => '64', 'label' => 'Districts Covered'],
        ];

        $team = [
            [
                'name' => 'Rafiq Ahmed',
                'position' => 'Founder & CEO',
                'bio' => '15+ years experience in electronics retail',
                'image' => 'https://images.unsplash.com/photo-1560250097-0b93528c311a?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80'
            ],
            [
                'name' => 'Sharmin Akter',
                'position' => 'Head of Operations',
                'bio' => 'Supply chain & logistics expert',
                'image' => 'https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80'
            ],
            [
                'name' => 'Tanvir Hasan',
                'position' => 'Customer Success Manager',
                'bio' => 'Dedicated to customer satisfaction',
                'image' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80'
            ],
        ];

        $values = [
            [
                'icon' => 'shield',
                'title' => 'Trust & Transparency',
                'description' => 'We believe in honest business practices and clear communication with our customers.'
            ],
            [
                'icon' => 'zap',
                'title' => 'Innovation First',
                'description' => 'Always bringing you the latest and most innovative tech products in the market.'
            ],
            [
                'icon' => 'heart',
                'title' => 'Customer Centric',
                'description' => 'Your satisfaction is our top priority. We go the extra mile for our customers.'
            ],
            [
                'icon' => 'trending-up',
                'title' => 'Continuous Growth',
                'description' => 'Constantly improving our services and expanding our product range.'
            ],
        ];

        return view('storefront.pages.about', compact('stats', 'team', 'values'));
    }

    /**
     * Show the FAQ page.
     */
    public function faq()
    {
        $faqCategories = [
            [
                'category' => 'Orders & Payment',
                'icon' => 'shopping-cart',
                'faqs' => [
                    [
                        'question' => 'How do I place an order?',
                        'answer' => 'Placing an order is easy! Simply browse our products, add items to your cart, and proceed to checkout. You can order as a guest or create an account for faster checkout in the future.'
                    ],
                    [
                        'question' => 'What payment methods do you accept?',
                        'answer' => 'We accept multiple payment methods including Cash on Delivery, bKash, Nagad, Rocket, and credit/debit cards. All digital payments are secure and encrypted.'
                    ],
                    [
                        'question' => 'Can I modify or cancel my order after placing it?',
                        'answer' => 'You can modify or cancel your order within 1 hour of placing it. Please contact our customer support immediately for assistance.'
                    ],
                    [
                        'question' => 'Is it safe to use my credit card on your website?',
                        'answer' => 'Absolutely! We use industry-standard SSL encryption and partner with trusted payment gateways to ensure your payment information is completely secure.'
                    ],
                ]
            ],
            [
                'category' => 'Shipping & Delivery',
                'icon' => 'truck',
                'faqs' => [
                    [
                        'question' => 'How long does shipping take?',
                        'answer' => 'Shipping typically takes 1-3 business days within Dhaka city and 3-7 business days for other cities in Bangladesh. Rural areas may take 7-10 business days.'
                    ],
                    [
                        'question' => 'How much does shipping cost?',
                        'answer' => 'Shipping costs BDT 100 for orders under BDT 5,000. Orders above BDT 5,000 qualify for free shipping!'
                    ],
                    [
                        'question' => 'Do you ship internationally?',
                        'answer' => 'Currently, we only ship within Bangladesh. However, we are planning to expand internationally soon!'
                    ],
                    [
                        'question' => 'How can I track my order?',
                        'answer' => 'Once your order is shipped, you will receive a tracking number via SMS and email. You can track your order from your account dashboard or through our courier partner\'s website.'
                    ],
                ]
            ],
            [
                'category' => 'Returns & Warranty',
                'icon' => 'refresh-cw',
                'faqs' => [
                    [
                        'question' => 'What is your return policy?',
                        'answer' => 'We offer a 7-day return policy on all products. Items must be unused and in original packaging. Some exceptions apply for hygiene products.'
                    ],
                    [
                        'question' => 'How do warranty claims work?',
                        'answer' => 'All products come with manufacturer warranty. For warranty claims, please contact our support team with your order number and a description of the issue.'
                    ],
                    [
                        'question' => 'What items are non-returnable?',
                        'answer' => 'Hygiene products, software downloads, and gift cards are non-returnable. Damaged or defective items are always eligible for replacement.'
                    ],
                    [
                        'question' => 'How long does the refund process take?',
                        'answer' => 'Once we receive and inspect your return, refunds are processed within 3-5 business days. The amount will be credited to your original payment method.'
                    ],
                ]
            ],
            [
                'category' => 'Products & Support',
                'icon' => 'headphones',
                'faqs' => [
                    [
                        'question' => 'Are your products genuine?',
                        'answer' => 'Yes! We source all products directly from authorized distributors and brand partners. Every product comes with a manufacturer warranty.'
                    ],
                    [
                        'question' => 'Do you offer technical support?',
                        'answer' => 'Absolutely! Our tech support team is available 7 days a week via phone, email, and live chat to help with any product issues.'
                    ],
                    [
                        'question' => 'Can I get a product demonstration?',
                        'answer' => 'We offer video demonstrations for select products. You can also visit our showroom in Dhaka for hands-on experience.'
                    ],
                    [
                        'question' => 'Do you price match?',
                        'answer' => 'Yes, we offer price matching for identical products from authorized Bangladeshi retailers. Contact us with proof of the lower price.'
                    ],
                ]
            ],
        ];

        return view('storefront.pages.faq', compact('faqCategories'));
    }

    /**
     * Show the Terms & Conditions page.
     */
    public function terms()
    {
        $lastUpdated = 'February 15, 2026';
        
        $sections = [
            [
                'title' => '1. Acceptance of Terms',
                'content' => 'By accessing and using ' . config('settings.store_name', 'GadgetBD') . ' ("the Website"), you accept and agree to be bound by these Terms and Conditions. If you do not agree to these terms, please do not use our website.',
                'icon' => 'check-circle'
            ],
            [
                'title' => '2. Eligibility',
                'content' => 'You must be at least 18 years old to make purchases on our website. By placing an order, you represent that you are legally capable of entering into binding contracts.',
                'icon' => 'user-check'
            ],
            [
                'title' => '3. Account Registration',
                'content' => 'When you create an account, you must provide accurate and complete information. You are responsible for maintaining the confidentiality of your account credentials and for all activities under your account.',
                'icon' => 'user-plus'
            ],
            [
                'title' => '4. Product Information',
                'content' => 'We strive to display accurate product information, including prices and specifications. However, we do not warrant that product descriptions or other content are error-free. Prices are subject to change without notice.',
                'icon' => 'info'
            ],
            [
                'title' => '5. Pricing and Payment',
                'content' => 'All prices are in Bangladeshi Taka (BDT) and include applicable VAT. We accept various payment methods as displayed at checkout. Orders are confirmed only after payment verification for online payments.',
                'icon' => 'credit-card'
            ],
            [
                'title' => '6. Order Acceptance',
                'content' => 'We reserve the right to refuse or cancel any order for reasons including product availability, errors in pricing, or suspected fraud. We will notify you if your order is cancelled.',
                'icon' => 'package'
            ],
            [
                'title' => '7. Shipping and Delivery',
                'content' => 'Delivery times are estimates and not guaranteed. Risk of loss passes to you upon delivery. We are not responsible for delays caused by courier services or customs.',
                'icon' => 'truck'
            ],
            [
                'title' => '8. Returns and Refunds',
                'content' => 'Our return policy is outlined separately. Please review it before making a purchase. Refunds are processed according to our return policy terms.',
                'icon' => 'rotate-ccw'
            ],
            [
                'title' => '9. Intellectual Property',
                'content' => 'All content on this website, including logos, images, and text, is our property or used with permission. You may not reproduce, distribute, or modify any content without written consent.',
                'icon' => 'copyright'
            ],
            [
                'title' => '10. Limitation of Liability',
                'content' => 'To the maximum extent permitted by law, ' . config('settings.store_name', 'GadgetBD') . ' shall not be liable for any indirect, incidental, or consequential damages arising from your use of our products or website.',
                'icon' => 'shield'
            ],
            [
                'title' => '11. Governing Law',
                'content' => 'These terms shall be governed by the laws of Bangladesh. Any disputes arising from these terms shall be subject to the exclusive jurisdiction of the courts in Dhaka, Bangladesh.',
                'icon' => 'scale'
            ],
        ];

        return view('storefront.pages.terms', compact('lastUpdated', 'sections'));
    }

    /**
     * Show the Privacy Policy page.
     */
    public function privacy()
    {
        $lastUpdated = 'February 15, 2026';
        
        $sections = [
            [
                'title' => '1. Information We Collect',
                'content' => 'We collect information you provide directly, such as name, email, phone number, and shipping address when you create an account or place an order. We also automatically collect certain information about your device and browsing behavior.',
                'icon' => 'database'
            ],
            [
                'title' => '2. How We Use Your Information',
                'content' => 'We use your information to process orders, improve our services, send promotional offers (with your consent), and communicate with you about your account or transactions.',
                'icon' => 'settings'
            ],
            [
                'title' => '3. Information Sharing',
                'content' => 'We share your information with trusted third parties only as necessary to fulfill orders (e.g., shipping companies, payment processors) or comply with legal obligations. We never sell your personal data.',
                'icon' => 'share-2'
            ],
            [
                'title' => '4. Data Security',
                'content' => 'We implement industry-standard security measures including SSL encryption, firewalls, and secure servers to protect your personal information from unauthorized access.',
                'icon' => 'lock'
            ],
            [
                'title' => '5. Your Rights',
                'content' => 'You have the right to access, correct, or delete your personal information. You can manage your preferences through your account settings or by contacting our support team.',
                'icon' => 'shield-check'
            ],
            [
                'title' => '6. Cookies',
                'content' => 'We use cookies to enhance your browsing experience, analyze site traffic, and personalize content. You can control cookie preferences through your browser settings.',
                'icon' => 'cookie'
            ],
            [
                'title' => '7. Children\'s Privacy',
                'content' => 'Our website is not intended for children under 13. We do not knowingly collect information from children. If you believe a child has provided us with data, please contact us.',
                'icon' => 'users'
            ],
            [
                'title' => '8. Changes to This Policy',
                'content' => 'We may update this privacy policy periodically. We will notify you of significant changes by posting a notice on our website or contacting you directly.',
                'icon' => 'file-text'
            ],
        ];

        return view('storefront.pages.privacy', compact('lastUpdated', 'sections'));
    }

    /**
     * Show the Return Policy page.
     */
    public function returns()
    {
        $lastUpdated = 'February 15, 2026';
        
        $returnSteps = [
            [
                'step' => 1,
                'title' => 'Initiate Return',
                'description' => 'Log in to your account and navigate to your orders. Click on "Return Item" for the product you wish to return, or contact our support team within 7 days of delivery.',
                'icon' => 'edit'
            ],
            [
                'step' => 2,
                'title' => 'Get Approval',
                'description' => 'Our team will review your request and send you a Return Merchandise Authorization (RMA) number via email within 24-48 hours.',
                'icon' => 'check'
            ],
            [
                'step' => 3,
                'title' => 'Pack & Ship',
                'description' => 'Pack the item securely in its original packaging with all accessories. Include the RMA number on the package and ship it to our returns address.',
                'icon' => 'package'
            ],
            [
                'step' => 4,
                'title' => 'Inspection & Refund',
                'description' => 'Once we receive your return, we\'ll inspect the item within 3 business days and process your refund to the original payment method.',
                'icon' => 'refresh-cw'
            ],
        ];

        $conditions = [
            'Eligible for Return' => [
                'Unopened products in original packaging',
                'Defective or damaged items (with photo proof)',
                'Wrong item shipped (with photo proof)',
                'Items within 7 days of delivery',
            ],
            'Not Eligible for Return' => [
                'Opened or used products (unless defective)',
                'Hygiene products (earphones, headphones, wearables)',
                'Software, apps, or digital downloads',
                'Gift cards and vouchers',
                'Clearance or sale items',
                'Items without original packaging or accessories',
            ],
        ];

        $refundMethods = [
            [
                'method' => 'Cash on Delivery',
                'process' => 'Refund will be processed via bank transfer to your account. Please provide your bank details.',
                'time' => '5-7 business days',
            ],
            [
                'method' => 'bKash/Nagad/Rocket',
                'process' => 'Refund will be sent to your mobile wallet number.',
                'time' => '3-5 business days',
            ],
            // [
            //     'method' => 'Credit/Debit Card',
            //     'process' => 'Refund will be credited back to your card.',
            //     'time' => '7-10 business days',
            // ],
        ];

        return view('storefront.pages.returns', compact('lastUpdated', 'returnSteps', 'conditions', 'refundMethods'));
    }
}